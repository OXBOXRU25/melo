<?php
/**
 * Шапка сайта и открывающая часть документа.
 *
 * @package melo
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>

	<script>
		/* Блоки прячутся до появления только при живом JS. Если main.js
		   не поднимется за 2.5 с — показываем всё принудительно. */
		(function () {
			var d = document.documentElement;
			d.classList.add('js');
			setTimeout(function () {
				if (!d.classList.contains('reveal-ready')) d.classList.add('reveal-failsafe');
			}, 2500);
		})();
	</script>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ============ SVG-спрайт ============ -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
	<symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
		<path d="M4 12h15M13 6l6 6-6 6"/>
	</symbol>
	<symbol id="i-plot-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M3 4h13v9"/>
		<path d="M3 4v16h9"/>
		<path d="M3 12h6"/>
		<circle cx="17" cy="17" r="4"/>
		<path d="M20 20l1.6 1.6"/>
	</symbol>
	<symbol id="i-house-plan" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M2.5 10.5 12 3l9.5 7.5"/>
		<path d="M5 12.5V21h14v-8.5"/>
		<path d="M10 21v-5h4v5"/>
	</symbol>
	<symbol id="i-site-plan" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M3 3.5h18v17H3z"/>
		<path d="M3 13h8v7.5"/>
		<path d="M11 13V8h10"/>
		<circle cx="16.5" cy="17" r="2.2"/>
	</symbol>
	<symbol id="i-structure" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M3 20.5h18"/>
		<path d="M5 20.5V9l7-4.5L19 9v11.5"/>
		<path d="M5 12.5h14"/>
		<path d="M5 16.5h14"/>
		<path d="M12 12.5v8"/>
	</symbol>
	<symbol id="i-permit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M14 2.5H6.5a1 1 0 0 0-1 1v17a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1V7z"/>
		<path d="M14 2.5V7h4.5"/>
		<path d="M8.5 15.5l2 2 4.5-4.5"/>
	</symbol>
</svg>

<!-- ============ Шапка ============ -->
<header class="site-header" id="top">
	<div class="container site-header__inner">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — на главную' ); ?>">
			<?php
			// Логотип из настройщика, если загружен; иначе файл темы.
			$melo_logo_src = false;
			if ( has_custom_logo() ) {
				$melo_logo_src = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
			}

			if ( $melo_logo_src ) :
				?>
				<img class="logo__mark" src="<?php echo esc_url( $melo_logo_src[0] ); ?>" alt=""
					width="<?php echo esc_attr( $melo_logo_src[1] ); ?>" height="<?php echo esc_attr( $melo_logo_src[2] ); ?>">
			<?php else : ?>
				<img class="logo__mark" src="<?php echo esc_url( melo_img( 'logo.png' ) ); ?>" alt="" width="196" height="54">
			<?php endif; ?>
		</a>

		<nav class="nav" aria-label="<?php esc_attr_e( 'Основное меню', 'melo' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'depth'          => 1,
					'walker'         => new Melo_Nav_Walker(),
					'link_class'     => 'nav__link',
					'fallback_cb'    => 'melo_fallback_primary_nav',
				)
			);
			?>
		</nav>

		<a class="site-header__phone" href="tel:<?php echo esc_attr( melo_contact( 'phone_raw' ) ); ?>">
			<?php echo esc_html( melo_contact( 'phone' ) ); ?>
		</a>

		<button class="burger" type="button" aria-expanded="false" aria-label="<?php esc_attr_e( 'Меню', 'melo' ); ?>"><span></span></button>
	</div>
</header>

<main>
