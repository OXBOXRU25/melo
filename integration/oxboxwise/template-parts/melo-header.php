<?php
/**
 * MELO — шапка внутренних страниц
 *
 * Собственный каркас документа: страницы MELO идут в новой стилистике,
 * поэтому шапка и подвал у них свои, а не из темы. Каркас полный —
 * doctype, head, wp_head(), body — чтобы не тянуть header.php темы
 * со старой шапкой.
 *
 * Подключается из шаблонов: get_template_part( 'template-parts/melo', 'header' );
 *
 * @package oxboxwise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$melo_dir = get_template_directory_uri() . '/img/melo/';
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/css/melo-page.css' ); ?>?v=<?php echo esc_attr( filemtime( get_template_directory() . '/css/melo-page.css' ) ); ?>">

	<?php
	/* Блоки прячутся до появления только при живом JS: класс melo-js ставит
	   скрипт ниже. Если melo-page.js не поднимется за 2.5 секунды, срабатывает
	   аварийный показ. Не удаляйте — иначе при сбое скрипта страница окажется
	   пустой. */
	?>
	<script>
	(function () {
		var d = document.documentElement;
		d.classList.add('melo-js');
		setTimeout(function () {
			if (!d.classList.contains('melo-reveal-ready')) d.classList.add('melo-reveal-failsafe');
		}, 2500);
	})();
	</script>

	<script defer src="<?php echo esc_url( get_template_directory_uri() . '/js/melo-page.js' ); ?>?v=<?php echo esc_attr( filemtime( get_template_directory() . '/js/melo-page.js' ) ); ?>"></script>

	<?php wp_head(); ?>
</head>

<body <?php body_class( 'melo-body' ); ?>>
<?php wp_body_open(); ?>

<div class="melo-page">

	<svg width="0" height="0" style="position:absolute" aria-hidden="true">
		<symbol id="melo-i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
			<path d="M4 12h15M13 6l6 6-6 6"/>
		</symbol>
		<symbol id="melo-i-plot-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
			<path d="M3 4h13v9"/><path d="M3 4v16h9"/><path d="M3 12h6"/>
			<circle cx="17" cy="17" r="4"/><path d="M20 20l1.6 1.6"/>
		</symbol>
		<symbol id="melo-i-house-plan" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
			<path d="M2.5 10.5 12 3l9.5 7.5"/><path d="M5 12.5V21h14v-8.5"/><path d="M10 21v-5h4v5"/>
		</symbol>
		<symbol id="melo-i-site-plan" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
			<path d="M3 3.5h18v17H3z"/><path d="M3 13h8v7.5"/><path d="M11 13V8h10"/>
			<circle cx="16.5" cy="17" r="2.2"/>
		</symbol>
		<symbol id="melo-i-structure" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
			<path d="M3 20.5h18"/><path d="M5 20.5V9l7-4.5L19 9v11.5"/>
			<path d="M5 12.5h14"/><path d="M5 16.5h14"/><path d="M12 12.5v8"/>
		</symbol>
		<symbol id="melo-i-permit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
			<path d="M14 2.5H6.5a1 1 0 0 0-1 1v17a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1V7z"/>
			<path d="M14 2.5V7h4.5"/><path d="M8.5 15.5l2 2 4.5-4.5"/>
		</symbol>
	</svg>

	<header class="melo-site-header" id="melo-top">
		<div class="melo-container melo-site-header__inner">
			<a class="melo-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — на главную' ); ?>">
				<img class="melo-logo__mark" src="<?php echo esc_url( $melo_dir . 'logo.png' ); ?>" alt="" width="196" height="54">
			</a>

			<nav class="melo-nav" aria-label="Основное меню">
				<?php
				/* Меню берётся из админки: Внешний вид → Меню, область «Главное».
				   Пока не назначено — показывается запасной набор. */
				melo_nav(
					'menu_main',
					'melo-nav__link',
					array(
						'Направления' => '/#directions',
						'О нас'       => '/#about',
						'Портфолио'   => '/#projects',
						'Цены'        => '/#pricing',
						'Контакты'    => '#contacts',
					)
				);
				?>
			</nav>

			<a class="melo-site-header__phone" href="tel:<?php echo esc_attr( melo_contact( 'phone_raw' ) ); ?>"><?php echo esc_html( melo_contact( 'phone' ) ); ?></a>

			<button class="melo-burger" type="button" aria-expanded="false" aria-label="Меню"><span></span></button>
		</div>
	</header>

	<main>
