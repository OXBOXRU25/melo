<?php
/**
 * Разовое заполнение полей MELO нынешними текстами.
 *
 * Зачем. Поля объявлены в inc/melo-fields.php, и пустое поле показывает
 * запасное значение — страница выглядит правильно. Но заказчик, открыв
 * админку, увидит пустые формы и не поймёт, откуда берётся текст.
 * Скрипт кладёт в поля ровно то, что сейчас на странице.
 *
 * Картинки из папки темы попутно заводятся в медиатеку: поле «Фото» в
 * ACF хранит идентификатор вложения, а файл темы им не является. Без
 * этого шага заказчик увидел бы карточку без картинки и не смог бы её
 * поменять.
 *
 * Запуск (файл кладётся в корень сайта и удаляется сразу после):
 *   https://сайт/melo-seed-fields.php?k=КЛЮЧ         — заполнить пустые
 *   https://сайт/melo-seed-fields.php?k=КЛЮЧ&dry=1   — только показать
 *   https://сайт/melo-seed-fields.php?k=КЛЮЧ&force=1 — перезаписать всё
 *
 * Без ключа отдаёт 404: файл на боевом сайте не должен обнаруживаться
 * перебором. По умолчанию заполняются только ПУСТЫЕ поля — правки
 * заказчика скрипт не затирает.
 */

define( 'MELO_SEED_KEY', 'melo-2026-08-24' );

require __DIR__ . '/wp-load.php';

if ( ! isset( $_GET['k'] ) || MELO_SEED_KEY !== $_GET['k'] ) {
	status_header( 404 );
	nocache_headers();
	exit;
}

header( 'Content-Type: text/plain; charset=utf-8' );

$dry   = isset( $_GET['dry'] );
$force = isset( $_GET['force'] );

if ( ! function_exists( 'melo_defaults' ) || ! function_exists( 'acf_get_field_group' ) ) {
	echo 'Не найдены melo_defaults() или ACF — проверьте, что тема и плагин на месте.' . PHP_EOL;
	exit;
}

require_once ABSPATH . 'wp-admin/includes/image.php';

/* Какая группа полей отвечает за какой шаблон. */
$map = array(
	'templates/template-melo-contacts.php'   => array( 'contacts', 'group_melo_contacts' ),
	'templates/template-melo-service.php'    => array( 'service', 'group_melo_service' ),
	'templates/template-melo-direction.php'  => array( 'direction', 'group_melo_direction' ),
	'templates/template-mainpage-updated.php' => array( 'home', 'group_melo_home' ),
);

/* Какие поля держат картинку: их значение — не текст, а вложение. */
$image_fields = array( 'melo_service_image', 'img' );

/**
 * Вложение медиатеки для файла из папки темы.
 *
 * Ищем по своей метке, чтобы повторный запуск не наплодил копий.
 *
 * @param string $file Имя файла в img/melo/.
 * @param string $alt  Описание картинки.
 * @return int Идентификатор вложения или 0.
 */
function melo_seed_attachment( $file, $alt = '' ) {
	global $dry;

	$found = get_posts( array(
		'post_type'   => 'attachment',
		'post_status' => 'inherit',
		'numberposts' => 1,
		'meta_key'    => '_melo_seed_src',
		'meta_value'  => $file,
		'fields'      => 'ids',
	) );
	if ( $found ) {
		return (int) $found[0];
	}

	$path = get_template_directory() . '/img/melo/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	/* Пробный прогон обязан быть безвредным: иначе «просто посмотреть»
	   уже засоряет медиатеку боевого сайта. */
	if ( $dry ) {
		return 'завести ' . $file;
	}

	$bits = wp_upload_bits( $file, null, file_get_contents( $path ) );
	if ( ! empty( $bits['error'] ) ) {
		return 0;
	}

	$type = wp_check_filetype( $bits['file'] );
	$id   = wp_insert_attachment( array(
		'post_mime_type' => $type['type'],
		'post_title'     => $alt ? $alt : pathinfo( $file, PATHINFO_FILENAME ),
		'post_status'    => 'inherit',
	), $bits['file'] );

	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $bits['file'] ) );
	update_post_meta( $id, '_melo_seed_src', $file );
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return (int) $id;
}

/**
 * Значение в вид, который принимает поле.
 *
 * Списки из массива превращаются в строки — под многострочное поле;
 * картинки — в идентификатор вложения.
 *
 * @param string $name  Имя поля или подполя.
 * @param mixed  $value Значение из melo_defaults().
 * @return mixed
 */
function melo_seed_value( $name, $value ) {
	global $image_fields;

	if ( in_array( $name, $image_fields, true ) && is_string( $value ) && '' !== $value ) {
		return melo_seed_attachment( $value );
	}

	if ( 'items' === $name && is_array( $value ) ) {
		return implode( chr( 10 ), $value );
	}

	return $value;
}

$total = 0;

foreach ( $map as $tpl => $pair ) {
	list( $group_slug, $group_key ) = $pair;

	$melo_pages = get_posts( array(
		'post_type'   => 'page',
		'post_status' => 'any',
		'numberposts' => -1,
		'meta_key'    => '_wp_page_template',
		'meta_value'  => $tpl,
		'fields'      => 'ids',
	) );

	if ( ! $melo_pages ) {
		echo '— страниц на шаблоне ' . $tpl . ' нет' . PHP_EOL . PHP_EOL;
		continue;
	}

	/* Ключи полей берём у самой группы, а не переписываем руками:
	   имя melo_lead есть в трёх группах с разными ключами, и запись по
	   имени легла бы не туда. */
	$group = acf_get_field_group( $group_key );
	if ( ! $group ) {
		echo '!! группа ' . $group_key . ' не зарегистрирована' . PHP_EOL;
		continue;
	}
	$keys = array();
	foreach ( acf_get_fields( $group ) as $f ) {
		if ( ! empty( $f['name'] ) ) {
			$keys[ $f['name'] ] = $f['key'];
		}
	}

	$defaults = melo_defaults( $group_slug );

	foreach ( $melo_pages as $pid ) {
		echo get_the_title( $pid ) . ' (id ' . $pid . ', ' . $tpl . ')' . PHP_EOL;

		foreach ( $defaults as $name => $value ) {
			if ( ! isset( $keys[ $name ] ) ) {
				echo '    ? нет поля ' . $name . PHP_EOL;
				continue;
			}

			$current = get_field( $keys[ $name ], $pid );
			$filled  = ! ( null === $current || false === $current || '' === $current || array() === $current );

			if ( $filled && ! $force ) {
				echo '    = ' . $name . ' — уже заполнено, не трогаю' . PHP_EOL;
				continue;
			}

			/* Готовим значение: строки списков и картинки приводятся к
			   тому виду, который поле умеет хранить. */
			if ( is_array( $value ) && isset( $value[0] ) && is_array( $value[0] ) ) {
				$rows = array();
				foreach ( $value as $row ) {
					$out = array();
					/* Описание картинки берём из той же строки: вложение
					   создаётся один раз, и alt в медиатеке должен быть
					   осмысленным, а не именем файла. */
					$row_alt = isset( $row['alt'] ) ? $row['alt'] : '';
					foreach ( $row as $sub => $sub_value ) {
						if ( 'img' === $sub && is_string( $sub_value ) && '' !== $sub_value ) {
							$out[ $sub ] = melo_seed_attachment( $sub_value, $row_alt );
							continue;
						}
						$out[ $sub ] = melo_seed_value( $sub, $sub_value );
					}
					$rows[] = $out;
				}
				$prepared = $rows;
				$note     = count( $rows ) . ' строк';
			} else {
				$prepared = melo_seed_value( $name, $value );
				$note     = is_string( $prepared ) ? '«' . mb_substr( $prepared, 0, 48 ) . '»' : $prepared;
			}

			if ( '' === $prepared || array() === $prepared || 0 === $prepared ) {
				echo '    · ' . $name . ' — нечего класть, оставляю пустым' . PHP_EOL;
				continue;
			}

			if ( $dry ) {
				echo '    ~ ' . $name . ' <- ' . $note . PHP_EOL;
			} else {
				update_field( $keys[ $name ], $prepared, $pid );
				echo '    + ' . $name . ' <- ' . $note . PHP_EOL;
				$total++;
			}
		}
		echo PHP_EOL;
	}
}

/* ---------------------------------------------------------------------
 * Старый текст в редакторе «Контактов»
 *
 * Шаблон контактов не выводит содержимое редактора, и в базе там с
 * прежнего проекта лежит текст про cookie. На сайте его не видно, а в
 * админке он и создаёт ощущение чужой страницы.
 *
 * Чистим только по явному флагу и с копией в своё мета-поле. WordPress
 * вдобавок сохраняет ревизию, так что вернуть можно двумя способами.
 * ------------------------------------------------------------------ */
if ( isset( $_GET['clean'] ) ) {
	echo PHP_EOL . 'Чистка редактора «Контактов»:' . PHP_EOL;

	$contacts = get_posts( array(
		'post_type'   => 'page',
		'post_status' => 'any',
		'numberposts' => -1,
		'meta_key'    => '_wp_page_template',
		'meta_value'  => 'templates/template-melo-contacts.php',
		'fields'      => 'ids',
	) );

	foreach ( $contacts as $pid ) {
		$old = get_post_field( 'post_content', $pid );
		if ( '' === trim( wp_strip_all_tags( $old ) ) ) {
			echo '  = id ' . $pid . ' — редактор уже пуст' . PHP_EOL;
			continue;
		}
		if ( $dry ) {
			echo '  ~ id ' . $pid . ' — очистил бы ' . mb_strlen( $old ) . ' символов' . PHP_EOL;
			continue;
		}
		update_post_meta( $pid, '_melo_old_content', $old );
		wp_update_post( array( 'ID' => $pid, 'post_content' => '' ) );
		echo '  + id ' . $pid . ' — очищено ' . mb_strlen( $old ) . ' символов, копия в _melo_old_content' . PHP_EOL;
	}
}

echo PHP_EOL . ( $dry ? 'Пробный прогон, ничего не записано.' . PHP_EOL : 'Записано полей: ' . $total . PHP_EOL );
