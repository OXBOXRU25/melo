/* ============================================================
   MELO — сборка пакета интеграции в тему oxboxwise

   Делает две вещи, и обе обязательны:

   1. СКОУПИНГ. Каждый селектор получает .melo-page, глобальные (html,
      body, :root, *) переписываются на сам контейнер. Это защищает САЙТ
      ОТ НАС: наш сброс на html/body/img/a/ul/h1-h4 не тронет их страницы.

   2. ПРЕФИКС КЛАССОВ. Каждый наш класс становится melo-*. Это защищает
      НАС ОТ САЙТА — и без второго шага первый бесполезен. У темы заняты
      container, btn, nav, nav__link, breadcrumbs, service, service__item,
      service__title, project__title. Их правила протекают внутрь наших
      блоков по тем свойствам, которые мы не задаём: так секция «Что
      входит в услугу» получила синий фон от их .service.

   Запуск: node tools/build-integration.mjs
   ============================================================ */

import fs from 'fs';
import path from 'path';

const ROOT = 'D:/AI/MELO/';
const OUT = ROOT + 'integration/';
const PREFIX_SEL = '.melo-page';
const P = 'melo-';

/* классы темы сайта, которые мы используем сознательно — не трогаем */
const KEEP = new Set(['open-modal-os', 'melo-page']);

/* ---------- 1. собираем список наших классов ---------- */
const cssSrc = ['tokens.css', 'base.css', 'components.css']
  .map(f => fs.readFileSync(ROOT + 'assets/css/' + f, 'utf8')).join('\n');

const ours = new Set();
for (const m of cssSrc.matchAll(/\.(-?[_a-zA-Z][\w-]*)/g)) ours.add(m[1]);
for (const f of ['dizayn-interera-i-eksterera.html', 'arhitekturnoe-proektirovanie.html']) {
  const html = fs.readFileSync(ROOT + f, 'utf8');
  for (const m of html.matchAll(/\sclass="([^"]+)"/g)) m[1].trim().split(/\s+/).forEach(c => c && ours.add(c));
}
/* служебные классы на <html>, их ставит скрипт */
['js', 'is-visible', 'reveal-failsafe', 'reveal-ready'].forEach(c => ours.add(c));
KEEP.forEach(c => ours.delete(c));

/* ложные срабатывания от регекса по CSS: .jpg, .css в комментариях и т.п. */
['jpg', 'png', 'css', 'ru', 'html', 'js-none'].forEach(c => { if (!/^(js)$/.test(c)) ours.delete(c); });
ours.add('js');

const rename = new Map([...ours].map(c => [c, P + c]));

/* ---------- 2. разбор CSS на блоки ---------- */
function splitBlocks(css) {
  const out = [];
  let depth = 0, start = 0, inStr = null, inComment = false;
  for (let i = 0; i < css.length; i++) {
    const c = css[i], n = css[i + 1];
    if (inComment) { if (c === '*' && n === '/') { inComment = false; i++; } continue; }
    if (inStr) { if (c === '\\') i++; else if (c === inStr) inStr = null; continue; }
    if (c === '/' && n === '*') { inComment = true; i++; continue; }
    if (c === '"' || c === "'") { inStr = c; continue; }
    if (c === '{') depth++;
    else if (c === '}') { depth--; if (depth === 0) { out.push(css.slice(start, i + 1)); start = i + 1; } }
    else if (c === ';' && depth === 0) { out.push(css.slice(start, i + 1)); start = i + 1; }
  }
  const tail = css.slice(start);
  if (tail.trim()) out.push(tail);
  return out;
}

const renameClasses = sel => sel.replace(/\.(-?[_a-zA-Z][\w-]*)/g, (m, name) =>
  rename.has(name) ? '.' + rename.get(name) : m);

function scopeOne(sel) {
  let s = sel.trim();
  if (!s) return s;
  if (s === ':root' || s === 'html' || s === 'body') return PREFIX_SEL;
  if (s === '*') return PREFIX_SEL + ', ' + PREFIX_SEL + ' *';
  const star = s.match(/^\*::(before|after)$/);
  if (star) return PREFIX_SEL + ' *::' + star[1];
  if (/^html\b/.test(s)) return PREFIX_SEL + renameClasses(s.replace(/^html/, ''));
  if (/^body\b/.test(s)) return PREFIX_SEL + renameClasses(s.replace(/^body/, ''));

  s = renameClasses(s);
  /* классы, которые скрипт вешает на <html> */
  const jsRoot = s.match(/^(\.melo-js(?:\.[\w-]+)*)\s+(.+)$/);
  if (jsRoot) return jsRoot[1] + ' ' + PREFIX_SEL + ' ' + jsRoot[2];
  if (/^\.melo-js(\.[\w-]+)*$/.test(s)) return s + ' ' + PREFIX_SEL;
  return PREFIX_SEL + ' ' + s;
}

function scopeRule(b) {
  const at = b.match(/^@([\w-]+)([^{]*)\{([\s\S]*)\}\s*$/);
  if (at) {
    const [, name, params, body] = at;
    if (name === 'keyframes' || name === '-webkit-keyframes') return '@' + name + ' ' + P + params.trim() + ' {' + body + '}';
    if (['media', 'supports', 'layer', 'container'].includes(name)) return '@' + name + params + ' {\n' + scope(body) + '\n}';
    return b;
  }
  if (b.startsWith('@')) return b;
  const m = b.match(/^([\s\S]*?)\{([\s\S]*)\}\s*$/);
  if (!m) return b;
  return m[1].split(',').map(scopeOne).join(',\n') + ' {' + m[2] + '}';
}

function scope(css) {
  return splitBlocks(css).map(block => {
    /* Ведущие комментарии отделяем: иначе они приклеиваются к правилу и
       @keyframes перестаёт опознаваться — имя не переименовывается,
       ссылка на него переименовывается, анимация умирает молча. */
    const lead = block.match(/^((?:\s*\/\*[\s\S]*?\*\/)*\s*)([\s\S]*)$/);
    const comments = lead[1].trim();
    const body = lead[2].trim();
    if (!body) return comments;
    const res = scopeRule(body);
    return comments ? comments + '\n' + res : res;
  }).filter(Boolean).join('\n\n');
}

let css = scope(cssSrc);
for (const kf of ['veil-in', 'hero-rise', 'hero-fade']) {
  css = css.replace(new RegExp('(animation(?:-name)?\\s*:[^;}]*?)\\b' + kf + '\\b', 'g'), '$1' + P + kf);
}
css = css.replace(/\.melo-page\s*\{\s*-webkit-text-size-adjust:\s*100%;\s*scroll-behavior:\s*smooth;\s*\}/g,
  '/* -webkit-text-size-adjust и scroll-behavior не переносим:\n   у темы сайта свои правила на html */');

const head = `/* ============================================================
   MELO — стили внутренних страниц для темы oxboxwise

   Две защиты, и нужны обе:

   1. Всё заскоуплено под .melo-page — наши правила не выходят наружу,
      сайт не трогается.
   2. Все классы с префиксом melo- — правила темы не протекают внутрь
      наших блоков. Без этого их .service красил нашу секцию в синий,
      а .container, .btn, .nav, .breadcrumbs ломали бы геометрию.

   Собрано скриптом tools/build-integration.mjs из tokens.css +
   base.css + components.css. Править исходники, не этот файл.
   ============================================================ */

`;

fs.mkdirSync(OUT + 'oxboxwise/css', { recursive: true });
fs.writeFileSync(OUT + 'oxboxwise/css/melo-page.css', head + css, 'utf8');

/* ---------- 3. те же замены в шаблонах и скрипте ---------- */
const files = [
  OUT + 'oxboxwise/templates/template-melo-service.php',
  OUT + 'oxboxwise/templates/template-melo-direction.php',
  OUT + 'oxboxwise/js/melo-page.js',
];

let touched = 0;
for (const f of files) {
  if (!fs.existsSync(f)) { console.log('!! нет файла: ' + path.basename(f)); continue; }
  let s = fs.readFileSync(f, 'utf8');

  /* class="a b c" в разметке */
  s = s.replace(/class="([^"]*)"/g, (m, list) => 'class="' + list.split(/\s+/)
    .map(c => (rename.has(c) && !KEEP.has(c)) ? rename.get(c) : c).join(' ') + '"');

  /* селекторы в JS: querySelector('.card'), classList.add('is-visible') */
  s = s.replace(/(['"`])((?:\.|#)?[\w.#\[\]="'-]*?)\1/g, m => m);   // ничего, обрабатываем точечно ниже
  for (const [from, to] of rename) {
    s = s.replace(new RegExp("(querySelector(?:All)?\\(['\"][^'\"]*?)\\." + from + "\\b", 'g'), '$1.' + to);
    s = s.replace(new RegExp("(classList\\.(?:add|remove|contains|toggle)\\(')" + from + "(')", 'g'), '$1' + to + '$2');
    s = s.replace(new RegExp("(classList\\.add\\(\")" + from + "(\")", 'g'), '$1' + to + '$2');
  }
  /* инлайновый скрипт в шаблоне ставит .js и .reveal-failsafe строками */
  s = s.replace(/classList\.add\('js'\)/g, "classList.add('" + P + "js')")
       .replace(/classList\.add\('reveal-failsafe'\)/g, "classList.add('" + P + "reveal-failsafe')")
       .replace(/classList\.contains\('reveal-ready'\)/g, "classList.contains('" + P + "reveal-ready')")
       .replace(/classList\.add\('reveal-ready'\)/g, "classList.add('" + P + "reveal-ready')");

  fs.writeFileSync(f, s, 'utf8');
  touched++;
}

/* ---------- отчёт ---------- */
const conflicts = ['container', 'btn', 'nav', 'nav__link', 'breadcrumbs', 'service', 'service__item', 'service__title', 'project__title'];
console.log('классов переименовано: ' + rename.size);
console.log('из них известных конфликтов с темой: ' + conflicts.filter(c => rename.has(c)).length + ' / ' + conflicts.length);
console.log('файлов обработано: ' + touched);
console.log('CSS: ' + Math.round((head + css).length / 1024) + ' КБ → integration/oxboxwise/css/melo-page.css');
