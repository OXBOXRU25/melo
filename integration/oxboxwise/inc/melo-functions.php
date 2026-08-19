<?php
/**
 * MELO — вспомогательные функции внутренних страниц
 *
 * Подключается из functions.php темы одной строкой:
 *   require get_template_directory() . '/inc/melo-functions.php';
 *
 * Ничего не переопределяет и ни на что не вешается глобально — только
 * объявляет две функции, которыми пользуются шаблоны MELO.
 *
 * @package oxboxwise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Стили шапки и подвала — на всех страницах сайта.
 *
 * Шапка общая, поэтому её стили нельзя запирать под .melo-page. Файл
 * melo-chrome.css изолирован только префиксами классов и подключается
 * последним, чтобы перекрывать прежние правила темы там, где имена
 * всё-таки пересеклись бы.
 *
 * Стили самих страниц (melo-page.css) грузит их собственный каркас —
 * на других страницах они не нужны и не подключаются.
 */
function melo_enqueue_chrome() {
	$file = get_template_directory() . '/css/melo-chrome.css';

	wp_enqueue_style(
		'melo-fonts',
		'https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'melo-chrome',
		get_template_directory_uri() . '/css/melo-chrome.css',
		array( 'melo-fonts' ),
		file_exists( $file ) ? filemtime( $file ) : null
	);

	/* Точечные правки существующих секций темы — светлые «Услуги»
	   и ссылки на карточках. Идут после chrome, чтобы перекрывать. */
	$ovr = get_template_directory() . '/css/melo-overrides.css';
	wp_enqueue_style(
		'melo-overrides',
		get_template_directory_uri() . '/css/melo-overrides.css',
		array( 'melo-chrome' ),
		file_exists( $ovr ) ? filemtime( $ovr ) : null
	);

	$js = get_template_directory() . '/js/melo-page.js';
	wp_enqueue_script(
		'melo-page',
		get_template_directory_uri() . '/js/melo-page.js',
		array(),
		file_exists( $js ) ? filemtime( $js ) : null,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'melo_enqueue_chrome', 100 );

/**
 * Значок сайта.
 *
 * Ставится в wp_head с приоритетом 1, чтобы перекрыть значок из
 * настройщика WordPress, если он там задан.
 */
function melo_favicon() {
	$dir = get_template_directory_uri() . '/img/melo/';
	echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $dir . 'favicon-32.png' ) . '">' . "\n";
	echo '<link rel="icon" type="image/png" sizes="512x512" href="' . esc_url( $dir . 'favicon-512.png' ) . '">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $dir . 'favicon-180.png' ) . '">' . "\n";
}
add_action( 'wp_head', 'melo_favicon', 1 );

/* Значок из настройщика WordPress убираем: иначе в head два набора
   иконок и браузер берёт последний, то есть прежний. */
remove_action( 'wp_head', 'wp_site_icon', 99 );

/**
 * Стили и скрипт содержимого страниц MELO.
 *
 * Вызывается из шаблонов ДО get_header(), чтобы попасть в wp_head().
 * На остальных страницах сайта этот файл не грузится вовсе.
 */
function melo_enqueue_page() {
	$file = get_template_directory() . '/css/melo-page.css';
	wp_enqueue_style(
		'melo-page',
		get_template_directory_uri() . '/css/melo-page.css',
		array( 'melo-chrome' ),
		file_exists( $file ) ? filemtime( $file ) : null
	);
}

/**
 * Контакты для шапки и подвала страниц MELO.
 *
 * Значения по умолчанию — рыба из макета. Переопределяются фильтром,
 * без правки шаблонов:
 *
 *   add_filter( 'melo_contacts', function ( $data ) {
 *       $data['phone']     = '+7 843 000 00 00';
 *       $data['phone_raw'] = '+78430000000';
 *       return $data;
 *   } );
 *
 * @param string $key Ключ значения.
 * @return string
 */
function melo_contact( $key ) {
	static $data = null;

	if ( null === $data ) {
		$data = apply_filters(
			'melo_contacts',
			array(
				'phone'     => '+7 900 000 00 00',
				'phone_raw' => '+79000000000',
				'phone2'    => '8 800 000 00 00',
				'phone2_raw'=> '88000000000',
				'email'     => 'info@name.ru',
				'address'   => 'г. Казань, ул. Ленина 00',
				'claim'     => 'Всегда открыт для новых проектов и сотрудничества.',
				'telegram'  => '#',
				'max'       => '#',
				'policy'    => '#',
			)
		);
	}

	return isset( $data[ $key ] ) ? $data[ $key ] : '';
}

/**
 * Меню в разметке MELO.
 *
 * Выводит плоский список ссылок без обёрток — так же, как в макете.
 * Walker не используется намеренно: разметка нужна предсказуемая, а
 * стандартный вывод тянет за собой ul/li и свои классы.
 *
 * @param string $location   Область меню: menu_main или menu_footer.
 * @param string $link_class Класс ссылки.
 * @param array  $fallback   Запасной набор «подпись => адрес», пока меню не назначено.
 */
function melo_nav( $location, $link_class = '', $fallback = array() ) {
	$items = array();
	$menu  = false;

	/* Сначала как область меню (menu_main, menu_footer), затем как имя
	   самого меню — в теме оно зовётся «Главное меню» и выводится по имени,
	   а не через область. */
	if ( has_nav_menu( $location ) ) {
		$locations = get_nav_menu_locations();
		$menu      = wp_get_nav_menu_object( $locations[ $location ] );
	}
	if ( ! $menu ) {
		$menu = wp_get_nav_menu_object( $location );
	}
	if ( $menu ) {
		$items = wp_get_nav_menu_items( $menu->term_id );
	}

	if ( empty( $items ) ) {
		foreach ( $fallback as $label => $href ) {
			printf(
				'<a class="%s" href="%s">%s</a>',
				esc_attr( $link_class ),
				esc_url( $href ),
				esc_html( $label )
			);
		}
		return;
	}

	$current = get_the_ID();

	/* На главной пункты меню должны вести к разделам этой же страницы,
	   а не на внутренние. Подменяем адрес по последнему сегменту пути:
	   /projects/ -> #projects и так далее. Названия пунктов не трогаем,
	   поэтому переименование в админке ничего не сломает. */
	$to_anchor = array(
		'projects' => '#projects',
		'contacts' => '#contacts',
		'about'    => '#about',
		'services' => '#services',
		'areas'    => '#areas',
	);
	$on_front = is_front_page() || is_home();

	foreach ( $items as $item ) {
		if ( (int) $item->menu_item_parent !== 0 ) {
			continue;   // подпункты в этой разметке не предусмотрены
		}

		$is_current = ( 'page' === $item->object && (int) $item->object_id === (int) $current );

		$url = $item->url;

		if ( $on_front ) {
			/* на главной — якорь к разделу этой же страницы */
			$slug = trim( wp_parse_url( $url, PHP_URL_PATH ) ? wp_parse_url( $url, PHP_URL_PATH ) : '', '/' );
			$slug = $slug ? substr( strrchr( '/' . $slug, '/' ), 1 ) : '';
			if ( $slug && isset( $to_anchor[ $slug ] ) ) {
				$url = $to_anchor[ $slug ];
			}
		} elseif ( '' !== $url && '#' === $url[0] && '#contacts' !== $url ) {
			/* На внутренних страницах голый якорь вёл бы в никуда: таких
			   секций здесь нет. Разворачиваем на главную. Исключение —
			   #contacts: подвал есть на каждой странице. */
			$url = home_url( '/' ) . $url;
		}

		printf(
			'<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
			esc_attr( $link_class ),
			esc_url( $url ),
			$is_current ? ' aria-current="page"' : '',
			esc_html( $item->title )
		);
	}
}
