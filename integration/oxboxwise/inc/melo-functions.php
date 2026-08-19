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

	foreach ( $items as $item ) {
		if ( (int) $item->menu_item_parent !== 0 ) {
			continue;   // подпункты в этой разметке не предусмотрены
		}

		$is_current = ( 'page' === $item->object && (int) $item->object_id === (int) $current );

		printf(
			'<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
			esc_attr( $link_class ),
			esc_url( $item->url ),
			$is_current ? ' aria-current="page"' : '',
			esc_html( $item->title )
		);
	}
}
