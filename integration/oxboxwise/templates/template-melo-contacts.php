<?php
/**
 * Template Name: MELO — контакты
 * Template Post Type: page
 *
 * Страница контактов в стилистике MELO: интро и способы связи.
 *
 * Разделы «Как добраться», «Заявка» и «Реквизиты» сняты по решению
 * заказчика. Их разметка и стили лежат в истории git, в коммите с
 * первой сборкой страницы, — возвращаются оттуда целиком.
 *
 * Данные пока тестовые и лежат массивами ниже — правятся здесь либо
 * переносятся в ACF, разметка вывода при этом не меняется. Телефон,
 * почта и адрес берутся из melo_contact(), чтобы совпадать с шапкой и
 * подвалом: одно значение на весь сайт, а не три копии.
 *
 * Содержимое страницы из редактора здесь НЕ выводится намеренно: в базе
 * у /contacts/ лежит служебный текст политики обработки данных, на
 * странице контактов он не к месту. Понадобится редактируемый текст —
 * заводим поле ACF, а не возвращаем the_content().
 *
 * @package oxboxwise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

melo_enqueue_page();
get_header();

while ( have_posts() ) :
	the_post();

	$melo_dir = get_template_directory_uri() . '/img/melo/';

	$hero_bg = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	if ( ! $hero_bg ) {
		$hero_bg = $melo_dir . 'project-3.jpg';
	}

	$lead = 'Приезжайте в офис, звоните или напишите — обсудим задачу, сориентируем по срокам и бюджету. На письма отвечаем в течение рабочего дня.';

	/* ---------------------------------------------------------------
	 * Способы связи. icon — id символа из спрайта ниже.
	 * ------------------------------------------------------------- */
	$ways = array(
		array(
			'icon'  => 'melo-i-phone',
			'role'  => 'Телефон',
			'value' => melo_contact( 'phone' ),
			'href'  => 'tel:' . melo_contact( 'phone_raw' ),
			'note'  => 'Пн–Пт, 10:00–19:00',
		),
		array(
			'icon'  => 'melo-i-mail',
			'role'  => 'Почта',
			'value' => melo_contact( 'email' ),
			'href'  => 'mailto:' . melo_contact( 'email' ),
			'note'  => 'Отвечаем в течение рабочего дня',
		),
		array(
			'icon'  => 'melo-i-pin',
			'role'  => 'Офис',
			'value' => melo_contact( 'address' ),
			'href'  => '',
			'note'  => '2 этаж, вход со стороны сквера',
		),
		array(
			'icon'  => 'melo-i-clock',
			'role'  => 'Часы работы',
			'value' => 'Пн–Пт 10:00–19:00',
			'href'  => '',
			'note'  => 'Сб и Вс — по договорённости',
		),
	);

	/* Мессенджеры отдельной строкой: у них своя логика — это не «ещё
	   один контакт», а альтернативный канал того же разговора. */
	$messengers = array(
		array( 'label' => 'Telegram', 'href' => melo_contact( 'telegram' ) ),
		array( 'label' => 'MAX',      'href' => melo_contact( 'max' ) ),
	);

	?>

<div class="melo-page melo-contacts-page">

	<?php /* Спрайт иконок: symbol вместо отдельных файлов — цвет
	         наследуется от текста, запросов ноль. */ ?>
	<svg class="melo-visually-hidden" aria-hidden="true" focusable="false">
		<symbol id="melo-i-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
			<path d="M6.5 3.5h3l1.5 4-2 1.5a12 12 0 0 0 6 6l1.5-2 4 1.5v3a1.5 1.5 0 0 1-1.6 1.5C11.6 19.5 4.5 12.4 4 5.1A1.5 1.5 0 0 1 5.5 3.5z"/>
		</symbol>
		<symbol id="melo-i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
			<rect x="3" y="5" width="18" height="14" rx="1.5"/><path d="M3.5 6.5 12 13l8.5-6.5"/>
		</symbol>
		<symbol id="melo-i-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
			<path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/>
		</symbol>
		<symbol id="melo-i-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
			<circle cx="12" cy="12" r="8.5"/><path d="M12 7v5.2l3.4 2"/>
		</symbol>
	</svg>

	<!-- ============ 1. Интро ============ -->
	<section class="melo-hero">
		<?php melo_hero_image( get_post_thumbnail_id(), 'project-3.jpg' ); ?>
		<div class="melo-hero__veil"></div>

		<div class="melo-container melo-hero__inner">
			<?php if ( function_exists( 'bcn_display_list' ) ) : ?>
			<nav class="melo-hero__crumbs" aria-label="Хлебные крошки">
				<ul class="melo-breadcrumbs">
					<?php bcn_display_list(); ?>
				</ul>
			</nav>
			<?php endif; ?>

			<h1><?php the_title(); ?></h1>

			<p class="melo-hero__lead"><?php echo esc_html( $lead ); ?></p>

			<?php /* класс open-modal-os и data-title — триггер попапа темы:
			       отдельной формы на странице нет, писать всё равно нужно. */ ?>
			<button class="melo-btn melo-btn--gold open-modal-os" type="button"
				data-title="Заявка со страницы контактов">
				Написать нам
			</button>
		</div>
	</section>

	<!-- ============ 2. Способы связи ============ -->
	<section class="melo-section melo-section--gray" id="melo-ways">
		<div class="melo-container">
			<div class="melo-section__head melo-section__head--center" data-reveal>
				<span class="melo-eyebrow">Связь</span>
				<h2>Как с нами связаться</h2>
			</div>

			<ul class="melo-contact-grid">
				<?php foreach ( $ways as $way ) : ?>
					<li class="melo-contact" data-reveal>
						<span class="melo-contact__icon" aria-hidden="true">
							<svg viewBox="0 0 24 24"><use href="#<?php echo esc_attr( $way['icon'] ); ?>"></use></svg>
						</span>
						<p class="melo-contact__role"><?php echo esc_html( $way['role'] ); ?></p>
						<?php if ( $way['href'] ) : ?>
							<a class="melo-contact__value ox-selectable" href="<?php echo esc_url( $way['href'] ); ?>"><?php echo esc_html( $way['value'] ); ?></a>
						<?php else : ?>
							<p class="melo-contact__value ox-selectable"><?php echo esc_html( $way['value'] ); ?></p>
						<?php endif; ?>
						<p class="melo-contact__note"><?php echo esc_html( $way['note'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>

			<p class="melo-contact-more" data-reveal>
				Пишите в мессенджеры, если так удобнее:
				<?php
				foreach ( $messengers as $i => $m ) :
					$tail = $i < count( $messengers ) - 1 ? ',' : '.';
					/* Пока адрес не заполнен, ссылки быть не должно: голая
					   решётка уводит страницу наверх, и это читается как
					   поломка. Ставим текст — заполнится, станет ссылкой. */
					if ( '' === $m['href'] || '#' === $m['href'] ) :
						?>
						<span><?php echo esc_html( $m['label'] ); ?></span><?php echo esc_html( $tail ); ?>
					<?php else : ?>
						<a href="<?php echo esc_url( $m['href'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $m['label'] ); ?></a><?php echo esc_html( $tail ); ?>
					<?php
					endif;
				endforeach;
				?>
			</p>
		</div>
	</section>

</div><!-- /.melo-page -->

	<?php
endwhile;

get_footer();
