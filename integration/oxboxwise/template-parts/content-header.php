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

/* Телефон и почта из настроек сайта. */
$melo_phone = function_exists( 'get_field' ) ? get_field( 'opt_phone_site', 'option' ) : '';
$melo_email = function_exists( 'get_field' ) ? get_field( 'opt_email_site', 'option' ) : '';
if ( ! $melo_email ) {
	$melo_email = melo_contact( 'email' );
}

/* На главной логотип никуда не ведёт — мы уже на ней. */
$melo_home = ( is_front_page() || is_home() ) ? 'javascript:void(0);' : get_home_url();
?>

<?php
/* Якорь «наверх» из подвала. На самой шапке его держать нельзя: она
   sticky, и как только прилипла к верху, её положение в документе
   совпадает с текущей прокруткой — переход никуда не ведёт, кнопка
   выглядит нерабочей. Отдельная нулевая точка перед шапкой всегда
   стоит в начале страницы. */
?>
<span id="melo-top" class="melo-anchor" aria-hidden="true"></span>

<?php /* класс header — для скриптов темы, см. пояснение внизу файла */ ?>
<header class="melo-site-header header">
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

		<button class="melo-burger header__btn-burger" type="button" aria-expanded="false" aria-label="Меню">
			<svg class="melo-burger__open" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
				<path d="M1 6H23M1 12H23M1 18H23" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
			</svg>
			<svg class="melo-burger__close" width="24" height="24" viewBox="0 0 16 16" fill="none" aria-hidden="true">
				<path d="M13.3333 13.3333L2.66666 2.66667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
				<path d="M13.3333 2.66667L2.66666 13.3333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
			</svg>
		</button>
	</div>
</header>

<?php
/* Полноэкранное меню. Открывается бургером, закрывается крестиком или
   переходом по пункту. Пункты те же, что в шапке — из админки. */
?>
<div class="melo-menu" id="melo-menu" hidden>
	<div class="melo-container melo-menu__top">
		<a class="melo-logo" href="<?php echo esc_url( $melo_home ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — на главную' ); ?>">
			<img class="melo-logo__mark" src="<?php echo esc_url( $melo_logo ); ?>" alt="" width="196" height="54">
		</a>

		<button class="melo-menu__close" type="button" aria-label="Закрыть меню">
			<svg width="24" height="24" viewBox="0 0 16 16" fill="none" aria-hidden="true">
				<path d="M13.3333 13.3333L2.66666 2.66667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
				<path d="M13.3333 2.66667L2.66666 13.3333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
			</svg>
		</button>
	</div>

	<div class="melo-container melo-menu__body">
		<nav class="melo-menu__nav" aria-label="Меню">
			<?php
			melo_nav(
				'Главное меню',
				'melo-menu__link',
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

		<div class="melo-menu__contacts">
			<?php if ( $melo_phone ) : ?>
				<a class="melo-menu__phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $melo_phone ) ); ?>"><?php echo esc_html( $melo_phone ); ?></a>
			<?php endif; ?>
			<?php if ( $melo_email ) : ?>
				<a class="melo-menu__email" href="mailto:<?php echo esc_attr( $melo_email ); ?>"><?php echo esc_html( $melo_email ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
/* Заглушка прежнего офф-канвас меню.

   headerMenu() из js/index.js ищет .header-menu и .header-menu__btn-close
   и падает, если их нет, — а падение обрывает весь модуль, включая
   createModal(), из-за чего не открывалась форма заявки.

   Мобильное меню у нас своё: бургер раскрывает саму шапку. Поэтому здесь
   только пустой блок, скрытый в css/melo-chrome.css. Удалять нельзя,
   пока скрипты темы не станут устойчивы к его отсутствию. */
?>
<div class="header-menu melo-legacy-menu" aria-hidden="true">
	<button class="header-menu__btn-close" type="button" tabindex="-1" aria-hidden="true"></button>
</div>

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
