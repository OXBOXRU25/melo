<?php
/**
 * Ищет и убирает скругление, нарисованное в самом файле.
 *
 * CSS такое не лечит: углы залиты белым прямо в пикселях, и на сером
 * фоне сайта они читаются как светлые уголки. Признак — белый клин,
 * нарастающий к углу, при том что середина той же кромки не белая.
 * Просто «светлый угол» так не выглядит: у него нет ступенчатой границы
 * по диагонали и он редко совпадает во всех четырёх углах сразу.
 *
 * Лечение — обрезка на радиус с каждой стороны. Кадр теряет по краю,
 * зато углы становятся честно прямыми.
 *
 *   php tools/unround-images.php            — только поиск
 *   php tools/unround-images.php --fix      — найти и обрезать
 */

$roots = array(
	'D:/AI/melo-local/melodesign.ru/wp-content/uploads',
	'D:/AI/MELO/integration/oxboxwise/img/melo',
);
$fix = in_array( '--fix', $argv, true );

/** Белый ли пиксель. Порог не 255: JPEG заливку слегка размывает. */
function melo_is_white( $im, $x, $y ) {
	$c = imagecolorat( $im, $x, $y );
	return ( ( $c >> 16 ) & 255 ) > 246 && ( ( $c >> 8 ) & 255 ) > 246 && ( $c & 255 ) > 246;
}

/**
 * Радиус вшитого скругления или 0.
 *
 * Считаем длину белого клина по диагонали из каждого угла. Скруглением
 * признаём только если клин есть во ВСЕХ четырёх углах и середины кромок
 * при этом не белые — иначе это просто светлый кадр.
 */
function melo_baked_radius( $im ) {
	$w = imagesx( $im );
	$h = imagesy( $im );
	if ( $w < 40 || $h < 40 ) {
		return 0;
	}

	/* Середины кромок. Одной точки мало: у фотографии с окном в кадре
	   середина верхней кромки бывает белой сама по себе, и проверка
	   отсекала настоящее скругление. Берём по пять проб в центральной
	   трети и считаем кромку белой, только если белого там большинство
	   и притом с ОБЕИХ сторон — у скругления так не бывает. */
	$edge_white = function ( $horizontal ) use ( $im, $w, $h ) {
		$n = 0;
		for ( $k = 0; $k < 5; $k++ ) {
			$t = 0.35 + 0.075 * $k;
			if ( $horizontal ) {
				$x = (int) ( $w * $t );
				if ( melo_is_white( $im, $x, 0 ) && melo_is_white( $im, $x, $h - 1 ) ) {
					$n++;
				}
			} else {
				$y = (int) ( $h * $t );
				if ( melo_is_white( $im, 0, $y ) && melo_is_white( $im, $w - 1, $y ) ) {
					$n++;
				}
			}
		}
		return $n >= 3;
	};
	if ( $edge_white( true ) || $edge_white( false ) ) {
		return 0;
	}

	$corners = array( array( 0, 0, 1, 1 ), array( $w - 1, 0, -1, 1 ), array( 0, $h - 1, 1, -1 ), array( $w - 1, $h - 1, -1, -1 ) );
	$runs = array();

	foreach ( $corners as $c ) {
		list( $cx, $cy, $dx, $dy ) = $c;
		if ( ! melo_is_white( $im, $cx, $cy ) ) {
			return 0;   // хотя бы один угол не белый — скругления нет
		}
		$n = 0;
		for ( $i = 0; $i < 40; $i++ ) {
			if ( ! melo_is_white( $im, $cx + $dx * $i, $cy + $dy * $i ) ) {
				break;
			}
			$n++;
		}
		$runs[] = $n;
	}

	/* Берём самый длинный клин и добавляем пиксель на сглаженную кромку. */
	return max( $runs ) + 1;
}

$files = array();
foreach ( $roots as $root ) {
	if ( ! is_dir( $root ) ) {
		continue;
	}
	$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $it as $f ) {
		if ( preg_match( '/\.(jpe?g|png|webp)$/i', $f->getFilename() ) ) {
			$files[] = $f->getPathname();
		}
	}
}

printf( "проверяю файлов: %d\n\n", count( $files ) );
$found = 0;

foreach ( $files as $path ) {
	$ext = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
	$im  = false;
	if ( 'jpg' === $ext || 'jpeg' === $ext ) { $im = @imagecreatefromjpeg( $path ); }
	elseif ( 'png' === $ext ) { $im = @imagecreatefrompng( $path ); }
	elseif ( 'webp' === $ext ) { $im = @imagecreatefromwebp( $path ); }
	if ( ! $im ) { continue; }

	$r = melo_baked_radius( $im );
	if ( $r > 1 ) {
		$found++;
		printf( "  %-34s %4dx%-5d радиус ~%dpx", basename( $path ), imagesx( $im ), imagesy( $im ), $r );

		if ( $fix ) {
			$w = imagesx( $im ) - $r * 2;
			$h = imagesy( $im ) - $r * 2;
			$dst = imagecreatetruecolor( $w, $h );
			imagecopy( $dst, $im, 0, 0, $r, $r, $w, $h );
			if ( 'png' === $ext ) { imagepng( $dst, $path ); }
			elseif ( 'webp' === $ext ) { imagewebp( $dst, $path, 88 ); }
			else { imagejpeg( $dst, $path, 92 ); }
			imagedestroy( $dst );
			printf( "  ->  обрезано до %dx%d", $w, $h );
		}
		echo "\n";
	}
	imagedestroy( $im );
}

echo "\n" . ( $found ? "со скруглением: $found" . ( $fix ? ' (обрезаны)' : ' (запустите с --fix)' ) : 'вшитых скруглений не найдено' ) . "\n";
