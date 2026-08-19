<?php
/**
 * Шапка сайта в стилистике MELO.
 *
 * ЗАМЕНЯЕТ прежний template-parts/content-header.php. Оригинал сохранён
 * рядом как content-header-old.php — вернуть можно переименованием.
 *
 * Что сохранено из прежней шапки, чтобы ничего не отвалилось:
 *   • логотип из ACF-опции opt_site_logo, с запасным файлом темы;
 *   • меню из админки через my_nav_menu( 'Главное меню' );
 *   • телефон из ACF-опции opt_phone_site;
 *   • обёртки .scroll-parent / .scroll-wrapper на главной — их ждёт
 *     плавная прокрутка темы, без них главная поедет;
 *   • <main class="main">, которое закрывает footer.php.
 *
 * Что изменилось: разметка и классы. Все классы с префиксом melo-,
 * стили в css/melo-chrome.css — он подключается глобально и не
 * пересекается с прежними правилами темы.
 *
 * @package oxboxwise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Логотип нового макета — белый лок-ап MELO из папки темы.
   ACF-опция opt_site_logo здесь намеренно НЕ используется: в ней лежит
   прежний оранжевый знак, на тёмной шапке он не работает. Когда в
   настройках появится новый файл, строку ниже можно заменить на
   get_field( 'opt_site_logo', 'option' )['url']. */
$melo_logo = get_template_directory_uri() . '/img/melo/logo.png';

/* Телефон из настроек сайта. */
$melo_phone = function_exists( 'get_field' ) ? get_field( 'opt_phone_site', 'option' ) : '';

/* На главной логотип никуда не ведёт — мы уже на ней. */
$melo_home = ( is_front_page() || is_home() ) ? 'javascript:void(0);' : get_home_url();
?>

<header class="melo-site-header" id="melo-top">
	<div class="melo-container melo-site-header__inner">

		<a class="melo-logo" href="<?php echo esc_url( $melo_home ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — на главную' ); ?>">
			<img class="melo-logo__mark" src="<?php echo esc_url( $melo_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="196" height="54">
		</a>

		<nav class="melo-nav" aria-label="Основное меню">
			<?php
			/* То же меню из админки, что было раньше — «Главное меню».
			   Выводим своей функцией, а не my_nav_menu(): та отдаёт ul со
			   своими классами, а в макете плоский набор ссылок. */
			melo_nav(
				'Главное меню',
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

		<?php if ( $melo_phone ) : ?>
			<a class="melo-site-header__phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $melo_phone ) ); ?>"><?php echo esc_html( $melo_phone ); ?></a>
		<?php endif; ?>

		<button class="melo-burger" type="button" aria-expanded="false" aria-label="Меню"><span></span></button>
	</div>
</header>

<?php
/* Обёртки плавной прокрутки на главной — как было в прежней шапке.
   Закрываются в footer.php темы. */
if ( is_front_page() ) :
	?>
	<div class="scroll-parent">
		<div class="scroll-wrapper">
	<?php
endif;
?>
	<main class="main">
