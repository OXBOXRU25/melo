<?php
/* Ищет по краям светлую кайму в 1–3 пикселя и срезает её.
 *
 * Признак — не абсолютная белизна, а СКАЧОК яркости между крайней
 * полосой и соседней. Именно на этом прошлая проверка промахнулась:
 * порог «светлее 240» не поймал столбец со средней 239.4, и я сказал
 * заказчику, что кромки чистые, а она была видна глазом.
 *
 *   php trim-edges.php            — только поиск
 *   php trim-edges.php --fix      — найти и срезать
 */

$roots = array(
	'D:/AI/MELO/integration/oxboxwise/img/melo',
);
$fix   = in_array( '--fix', $argv, true );

/** Средняя яркость полосы: строки при $horizontal, иначе столбца. */
function melo_band( $im, $i, $horizontal ) {
	$w = imagesx( $im );
	$h = imagesy( $im );
	$s = 0;
	$n = 0;
	if ( $horizontal ) {
		for ( $x = 0; $x < $w; $x++ ) {
			$c  = imagecolorat( $im, $x, $i );
			$s += ( ( ( $c >> 16 ) & 255 ) + ( ( $c >> 8 ) & 255 ) + ( $c & 255 ) ) / 3;
			$n++;
		}
	} else {
		for ( $y = 0; $y < $h; $y++ ) {
			$c  = imagecolorat( $im, $i, $y );
			$s += ( ( ( $c >> 16 ) & 255 ) + ( ( $c >> 8 ) & 255 ) + ( $c & 255 ) ) / 3;
			$n++;
		}
	}
	return $s / $n;
}

/**
 * Сколько крайних полос светлее внутренней части минимум на 60 единиц.
 * Больше трёх не проверяем: кайма шире — это уже рамка в кадре, её
 * срезать самовольно нельзя.
 */
function melo_trim_side( $im, $side ) {
	$w = imagesx( $im );
	$h = imagesy( $im );
	$horizontal = ( 'top' === $side || 'bottom' === $side );
	$len        = $horizontal ? $h : $w;

	$at = function ( $k ) use ( $side, $w, $h ) {
		switch ( $side ) {
			case 'left':   return $k;
			case 'right':  return $w - 1 - $k;
			case 'top':    return $k;
			default:       return $h - 1 - $k;
		}
	};

	/* Опора — полоса на глубине 6: заведомо вне каймы. */
	$inner = melo_band( $im, $at( 6 ), $horizontal );

	$n = 0;
	for ( $k = 0; $k < 3; $k++ ) {
		if ( melo_band( $im, $at( $k ), $horizontal ) - $inner > 60 ) {
			$n++;
		} else {
			break;
		}
	}
	return $n;
}

$files = array();
foreach ( $roots as $root ) {
	foreach ( glob( $root . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE ) as $f ) {
		$files[] = $f;
	}
}

printf( "проверяю файлов: %d%s%s", count( $files ), PHP_EOL, PHP_EOL );
$found = 0;

foreach ( $files as $path ) {
	$ext = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
	$im  = false;
	if ( 'jpg' === $ext || 'jpeg' === $ext ) { $im = @imagecreatefromjpeg( $path ); }
	elseif ( 'png' === $ext ) { $im = @imagecreatefrompng( $path ); }
	elseif ( 'webp' === $ext ) { $im = @imagecreatefromwebp( $path ); }
	if ( ! $im ) { continue; }

	$w = imagesx( $im );
	$h = imagesy( $im );
	if ( $w < 40 || $h < 40 ) { imagedestroy( $im ); continue; }

	$cut = array(
		'left'   => melo_trim_side( $im, 'left' ),
		'right'  => melo_trim_side( $im, 'right' ),
		'top'    => melo_trim_side( $im, 'top' ),
		'bottom' => melo_trim_side( $im, 'bottom' ),
	);

	if ( array_sum( $cut ) > 0 ) {
		$found++;
		printf(
			"  %-18s %4dx%-5d кайма: слева %d, справа %d, сверху %d, снизу %d",
			basename( $path ), $w, $h, $cut['left'], $cut['right'], $cut['top'], $cut['bottom']
		);

		if ( $fix ) {
			$nw  = $w - $cut['left'] - $cut['right'];
			$nh  = $h - $cut['top'] - $cut['bottom'];
			$dst = imagecreatetruecolor( $nw, $nh );
			imagecopy( $dst, $im, 0, 0, $cut['left'], $cut['top'], $nw, $nh );
			if ( 'png' === $ext ) { imagepng( $dst, $path ); }
			elseif ( 'webp' === $ext ) { imagewebp( $dst, $path, 90 ); }
			else { imagejpeg( $dst, $path, 92 ); }
			imagedestroy( $dst );
			printf( "  ->  %dx%d", $nw, $nh );
		}
		echo PHP_EOL;
	}

	imagedestroy( $im );
}

echo PHP_EOL . ( $found
	? "с каймой: $found" . ( $fix ? ' (срезано)' : ' (запустите с --fix)' )
	: 'каймы не найдено' ) . PHP_EOL;
