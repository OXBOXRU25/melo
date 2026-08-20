<?php
/**
 * Нарезка фотографий первого экрана под srcset.
 *
 * Фото первого экрана — самая тяжёлая картинка страницы и почти всегда
 * тот самый элемент, по которому считается LCP. Одним файлом на 1920px
 * его отдавать нельзя: телефону приходит в 2,7 раза больше пикселей, чем
 * он способен показать.
 *
 * Кладём рядом с оригиналом файлы вида hero-1280.jpg. Оригинал остаётся
 * как есть — он же и самый широкий вариант.
 *
 * Запуск:  php tools/make-hero-sizes.php
 */

$dir    = __DIR__ . '/../integration/oxboxwise/img/melo';
$theme  = 'D:/AI/melo-local/melodesign.ru/wp-content/themes/oxboxwise/img/melo';
$files  = array( 'hero.jpg', 'project-3.jpg' );
$widths = array( 640, 960, 1280 );

printf( "%-22s %10s %8s\n", 'файл', 'размер', 'вес' );
echo str_repeat( '-', 44 ) . "\n";

foreach ( $files as $name ) {
	$src = $dir . '/' . $name;
	if ( ! file_exists( $src ) ) {
		echo "  нет файла: $name\n";
		continue;
	}

	$img = imagecreatefromjpeg( $src );
	$sw  = imagesx( $img );
	$sh  = imagesy( $img );
	printf( "%-22s %5dx%-4d %6d КБ  (оригинал)\n", $name, $sw, $sh, round( filesize( $src ) / 1024 ) );

	foreach ( $widths as $w ) {
		if ( $w >= $sw ) {
			continue;   // увеличивать нечего: резкости это не добавит
		}
		$h   = (int) round( $sh * $w / $sw );
		$dst = imagecreatetruecolor( $w, $h );
		imagecopyresampled( $dst, $img, 0, 0, 0, 0, $w, $h, $sw, $sh );

		$out = $dir . '/' . pathinfo( $name, PATHINFO_FILENAME ) . '-' . $w . '.jpg';
		imagejpeg( $dst, $out, 82 );
		imagedestroy( $dst );

		copy( $out, $theme . '/' . basename( $out ) );
		printf( "%-22s %5dx%-4d %6d КБ\n", basename( $out ), $w, $h, round( filesize( $out ) / 1024 ) );
	}
	imagedestroy( $img );
}
