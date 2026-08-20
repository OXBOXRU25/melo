/**
 * Обрезка шрифтов темы до нужных языков.
 *
 * Зачем. Тема кладёт Inter восемнадцатью начертаниями по ~116 КБ, и это
 * полный Юникод: греческий, вьетнамский, кириллица, латиница со всеми
 * диакритиками. Браузер качает три файла — 346 КБ, — и gzip тут не
 * помогает: woff2 уже сжат, замер даёт −1%.
 *
 * Оставляем кириллицу, латиницу, цифры, знаки препинания и типографику,
 * которую мы реально ставим в текстах: тире, кавычки-ёлочки, ×, ², №.
 *
 * Оригиналы кладутся рядом в _fonts-original — обрезка необратима.
 *
 * Запуск:  node tools/subset-fonts.mjs
 */
import subsetFont from 'subset-font';
import fs from 'node:fs';
import path from 'node:path';

const THEME = 'D:/AI/melo-local/melodesign.ru/wp-content/themes/oxboxwise/fonts';
const BACKUP = 'D:/AI/melo-local/_fonts-original';
const PKG = 'D:/AI/MELO/integration/oxboxwise/fonts';

/* Набор символов, который остаётся в шрифте. */
const ranges = [
  [0x0020, 0x007e],   // латиница, цифры, знаки препинания
  [0x00a0, 0x00ff],   // латиница-1: ©, °, ×, ± и подобное
  [0x0400, 0x045f],   // кириллица
  [0x0490, 0x0491],   // Ґ ґ
  [0x2010, 0x2027],   // тире, кавычки, многоточие
  [0x2030, 0x205e],   // ‰, ‹›, ‼, №-соседи
  [0x20a0, 0x20bf],   // валюты, включая ₽
  [0x2116, 0x2116],   // №
  [0x2190, 0x2193],   // стрелки
  [0x00b2, 0x00b3],   // ² ³ — «м²» стоит в проектах
  [0x2212, 0x2212],   // минус
];

let text = '';
for (const [a, b] of ranges) {
  for (let c = a; c <= b; c++) text += String.fromCodePoint(c);
}

fs.mkdirSync(BACKUP, { recursive: true });
fs.mkdirSync(PKG, { recursive: true });

const files = fs.readdirSync(THEME).filter(f => /\.woff2$/i.test(f));
if (!files.length) { console.log('шрифтов не нашлось в ' + THEME); process.exit(1); }

let was = 0, now = 0, done = 0;
console.log('файл'.padEnd(34) + 'было'.padStart(8) + 'стало'.padStart(9) + '  выигрыш');
console.log('-'.repeat(62));

for (const f of files) {
  const src = path.join(THEME, f);
  const bak = path.join(BACKUP, f);

  /* Режем всегда из оригинала: повторный запуск по уже обрезанному
     файлу ничего не сломает, но и смысла не имеет. */
  if (!fs.existsSync(bak)) fs.copyFileSync(src, bak);
  const input = fs.readFileSync(bak);

  try {
    const out = await subsetFont(input, text, { targetFormat: 'woff2' });
    fs.writeFileSync(src, out);
    fs.writeFileSync(path.join(PKG, f), out);
    was += input.length; now += out.length; done++;
    console.log(
      f.padEnd(34) +
      String(Math.round(input.length / 1024) + ' КБ').padStart(8) +
      String(Math.round(out.length / 1024) + ' КБ').padStart(9) +
      '   -' + Math.round(100 - (out.length * 100) / input.length) + '%'
    );
  } catch (e) {
    console.log(f.padEnd(34) + '  ОШИБКА: ' + e.message);
  }
}

console.log('-'.repeat(62));
console.log(
  `обрезано файлов: ${done}   было ${Math.round(was / 1024)} КБ, стало ${Math.round(now / 1024)} КБ` +
  `   -${Math.round(100 - (now * 100) / was)}%`
);
console.log('оригиналы: ' + BACKUP);
