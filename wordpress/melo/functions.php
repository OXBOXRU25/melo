<?php
/**
 * MELO — настройка темы.
 *
 * @package melo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MELO_VERSION', '1.0.0' );

/**
 * Возможности темы и меню.
 */
function melo_setup() {
	load_theme_textdomain( 'melo', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Основное меню (шапка)', 'melo' ),
			'footer'  => __( 'Меню в подвале', 'melo' ),
		)
	);
}
add_action( 'after_setup_theme', 'melo_setup' );

/**
 * Стили и скрипты.
 *
 * Порядок важен: tokens → base → components. Версия берётся из времени
 * изменения файла, поэтому кеш браузера сбрасывается сам после правок.
 */
function melo_enqueue_assets() {
	$uri = get_template_directory_uri();
	$dir = get_template_directory();

	$ver = static function ( $rel ) use ( $dir ) {
		$path = $dir . $rel;
		return file_exists( $path ) ? filemtime( $path ) : MELO_VERSION;
	};

	// Шрифты. Для российского хостинга их лучше положить локально — см. README.
	wp_enqueue_style(
		'melo-fonts',
		'https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&family=Playfair+Display:wght@400;500&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'melo-tokens', $uri . '/assets/css/tokens.css', array(), $ver( '/assets/css/tokens.css' ) );
	wp_enqueue_style( 'melo-base', $uri . '/assets/css/base.css', array( 'melo-tokens' ), $ver( '/assets/css/base.css' ) );
	wp_enqueue_style( 'melo-components', $uri . '/assets/css/components.css', array( 'melo-base' ), $ver( '/assets/css/components.css' ) );
	wp_enqueue_style( 'melo-style', get_stylesheet_uri(), array( 'melo-components' ), $ver( '/style.css' ) );

	wp_enqueue_script( 'melo-main', $uri . '/assets/js/main.js', array(), $ver( '/assets/js/main.js' ), true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'melo_enqueue_assets' );

/**
 * Ссылка на изображение темы с запасным вариантом.
 *
 * @param string $file Имя файла в assets/img.
 * @return string
 */
function melo_img( $file ) {
	return get_template_directory_uri() . '/assets/img/' . ltrim( $file, '/' );
}

/**
 * Обход меню, повторяющий вёрстку макета: плоский список голых ссылок,
 * без ul и li. Класс ссылки задаётся аргументом link_class.
 */
class Melo_Nav_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$link_class = isset( $args->link_class ) ? $args->link_class : 'nav__link';
		$classes    = (array) $item->classes;

		$is_current = in_array( 'current-menu-item', $classes, true )
			|| in_array( 'current_page_item', $classes, true )
			|| in_array( 'current-menu-ancestor', $classes, true );

		$attrs = '';
		if ( $link_class ) {
			$attrs .= ' class="' . esc_attr( $link_class ) . '"';
		}
		$attrs .= ' href="' . esc_url( $item->url ) . '"';
		if ( $is_current ) {
			$attrs .= ' aria-current="page"';
		}
		if ( $item->target ) {
			$attrs .= ' target="' . esc_attr( $item->target ) . '" rel="noopener"';
		}

		$output .= '<a' . $attrs . '>' . esc_html( $item->title ) . '</a>';
	}
}

/**
 * Меню шапки, когда в админке ещё ничего не назначено.
 */
function melo_fallback_primary_nav() {
	$items = array(
		array(
			'label' => __( 'Направления', 'melo' ),
			'url'   => home_url( '/' ),
		),
		array(
			'label' => __( 'О нас', 'melo' ),
			'url'   => home_url( '/' ),
		),
		array(
			'label' => __( 'Портфолио', 'melo' ),
			'url'   => home_url( '/' ),
		),
		array(
			'label' => __( 'Цены', 'melo' ),
			'url'   => home_url( '/' ),
		),
		array(
			'label' => __( 'Контакты', 'melo' ),
			'url'   => '#contacts',
		),
	);

	foreach ( $items as $item ) {
		printf(
			'<a class="nav__link" href="%s">%s</a>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
}

/**
 * Меню подвала, когда в админке ещё ничего не назначено.
 */
function melo_fallback_footer_nav() {
	melo_fallback_primary_nav();
}

/**
 * Контакты бюро. Значения правятся здесь либо переопределяются фильтром,
 * чтобы не искать их по шаблонам.
 *
 * @param string $key phone|phone_raw|phone_free|email|address|claim
 * @return string
 */
function melo_contact( $key ) {
	$data = apply_filters(
		'melo_contacts',
		array(
			'phone'      => '+7 900 000 00 00',
			'phone_raw'  => '+79000000000',
			'phone_free' => '8 800 000 00 00',
			'free_raw'   => '88000000000',
			'email'      => 'info@name.ru',
			'address'    => 'г. Казань, ул. Ленина 00',
			'claim'      => 'Всегда открыт для новых проектов и сотрудничества.',
			'telegram'   => '#',
			'max'        => '#',
		)
	);

	return isset( $data[ $key ] ) ? $data[ $key ] : '';
}
