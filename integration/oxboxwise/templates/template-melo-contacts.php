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
 * Содержимое правится в админке: поля объявлены в inc/melo-fields.php,
 * читаются через melo_field() / melo_rows(), а запасные значения берутся
 * оттуда же из melo_defaults(). Пустое поле показывает запасное значение,
 * а не пустоту.
 *
 * Телефон, почта и адрес по умолчанию берутся из melo_contact(), чтобы
 * совпадать с шапкой и подвалом: одно значение на весь сайт, а не три
 * копии.
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

	$d = melo_defaults( 'contacts' );

	$lead     = melo_field( 'melo_lead', $d['melo_lead'] );
	$hero_btn = melo_field( 'melo_hero_btn', $d['melo_hero_btn'] );

	$ways_eyebrow = melo_field( 'melo_ways_eyebrow', $d['melo_ways_eyebrow'] );
	$ways_title   = melo_field( 'melo_ways_title', $d['melo_ways_title'] );
	$msg_text     = melo_field( 'melo_messengers_text', $d['melo_messengers_text'] );

	/* ---------------------------------------------------------------
	 * Способы связи. icon — id символа из спрайта ниже.
	 * ------------------------------------------------------------- */
	$ways = melo_fill( melo_rows( 'melo_ways', $d['melo_ways'] ), array( 'icon', 'role', 'value', 'href', 'note' ) );

	/* Телефон, почта и адрес живут в общих настройках сайта — одно
	   значение на шапку, подвал и эту страницу. Поэтому пустое поле тут
	   не ошибка, а «взять оттуда»: заказчик правит контакт в одном месте
	   и не ищет вторую копию. */
	foreach ( $ways as $melo_i => $melo_way ) {
		$melo_icon = isset( $melo_way['icon'] ) ? $melo_way['icon'] : '';

		if ( empty( $melo_way['value'] ) ) {
			if ( 'melo-i-phone' === $melo_icon ) {
				$ways[ $melo_i ]['value'] = melo_contact( 'phone' );
			} elseif ( 'melo-i-mail' === $melo_icon ) {
				$ways[ $melo_i ]['value'] = melo_contact( 'email' );
			} elseif ( 'melo-i-pin' === $melo_icon ) {
				$ways[ $melo_i ]['value'] = melo_contact( 'address' );
			}
		}

		if ( empty( $melo_way['href'] ) ) {
			if ( 'melo-i-phone' === $melo_icon ) {
				$ways[ $melo_i ]['href'] = 'tel:' . melo_contact( 'phone_raw' );
			} elseif ( 'melo-i-mail' === $melo_icon ) {
				$ways[ $melo_i ]['href'] = 'mailto:' . melo_contact( 'email' );
			}
		}
	}

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
	<?php get_template_part( 'template-parts/melo-icons' ); ?>

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
				<?php echo esc_html( $hero_btn ); ?>
			</button>
		</div>
	</section>

	<!-- ============ 2. Способы связи ============ -->
	<section class="melo-section melo-section--gray" id="melo-ways">
		<div class="melo-container">
			<div class="melo-section__head melo-section__head--center" data-reveal>
				<span class="melo-eyebrow"><?php echo esc_html( $ways_eyebrow ); ?></span>
				<h2><?php echo esc_html( $ways_title ); ?></h2>
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
				<?php echo esc_html( $msg_text ); ?>
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
