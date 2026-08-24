<?php
/**
 * Template Name: MELO — страница услуги
 * Template Post Type: page
 *
 * Внутренняя страница услуги в новой стилистике MELO: интро с матовым
 * стеклом, «Что входит в услугу», «Как мы работаем», «Примеры реализации».
 *
 * Шапка и подвал — свои, в новой стилистике MELO: страницы идут в новом
 * дизайне, старая шапка темы на них не к месту. Каркас документа лежит
 * в template-parts/melo-header.php и melo-footer.php
 *
 * Весь контент обёрнут в <div class="melo-page">. Стили в
 * css/melo-page.css заскоуплены под этот класс и наружу не выходят,
 * поэтому остальные страницы сайта не затрагиваются.
 *
 * Содержимое блоков правится в админке: поля объявлены в
 * inc/melo-fields.php, читаются через melo_field() / melo_rows(), а
 * запасные значения берутся оттуда же из melo_defaults(). Пустое поле
 * показывает запасное значение, а не пустоту.
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

	/* ---------------------------------------------------------------
	 * Интро
	 * ------------------------------------------------------------- */
	$melo_dir = get_template_directory_uri() . '/img/melo/';

	$hero_bg = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	if ( ! $hero_bg ) {
		$hero_bg = $melo_dir . 'hero.jpg';
	}

	$d = melo_defaults( 'service' );

	/* Лид: сначала своё поле, потом краткое описание записи, и лишь затем
	   текст по умолчанию — так у заказчика есть и привычный «Отрывок», и
	   наше поле, если он его не найдёт. */
	$lead = get_the_excerpt();
	if ( ! $lead ) {
		$lead = $d['melo_lead'];
	}
	$lead = melo_field( 'melo_lead', $lead );

	/* ---------------------------------------------------------------
	 * Что входит в услугу
	 * icon — id символа из спрайта ниже.
	 * ------------------------------------------------------------- */
	$service_title = melo_field( 'melo_service_title', $d['melo_service_title'] );

	$service_image     = melo_field( 'melo_service_image', '' );
	$service_image_src = melo_img_src( $service_image, $d['melo_service_image'] );
	$service_image_alt = melo_img_alt( $service_image, melo_field( 'melo_service_image_alt', '' ), $d['melo_service_image_alt'] );

	$steps_eyebrow = melo_field( 'melo_steps_eyebrow', $d['melo_steps_eyebrow'] );
	$steps_title   = melo_field( 'melo_steps_title', $d['melo_steps_title'] );
	$works_eyebrow = melo_field( 'melo_works_eyebrow', $d['melo_works_eyebrow'] );
	$works_title   = melo_field( 'melo_works_title', $d['melo_works_title'] );

	$service_items = melo_fill( melo_rows( 'melo_service_items', $d['melo_service_items'] ), array( 'icon', 'title', 'text' ) );

	/* ---------------------------------------------------------------
	 * Как мы работаем
	 * ------------------------------------------------------------- */
	$steps = melo_fill( melo_rows( 'melo_steps', $d['melo_steps'] ), array( 'title', 'text' ) );

	/* ---------------------------------------------------------------
	 * Примеры реализации
	 * Чтобы тянуть из типа записи projects, который в теме уже есть,
	 * замените массив на WP_Query — разметка цикла не меняется.
	 * ------------------------------------------------------------- */
	$projects = array(
		array( 'img' => 'project-1.jpg', 'alt' => 'Интерьер квартиры-студии в светлых тонах', 'w' => 903, 'h' => 1004, 'title' => 'Квартира-студия', 'tag' => 'Интерьеры квартир', 'area' => '27', 'url' => '#' ),
		array( 'img' => 'project-2.jpg', 'alt' => 'Гостиная однокомнатной квартиры с каменной стеной', 'w' => 904, 'h' => 644, 'title' => 'Однокомнатная квартира', 'tag' => 'Интерьеры квартир', 'area' => '34', 'url' => '#' ),
		array( 'img' => 'project-3.jpg', 'alt' => 'Интерьер гостиной загородного дома', 'w' => 903, 'h' => 746, 'title' => 'Загородный дом', 'tag' => 'Интерьер загородного дома', 'area' => '70', 'url' => '#' ),
		array( 'img' => 'project-4.jpg', 'alt' => 'Летняя кухня с панорамными окнами', 'w' => 904, 'h' => 1004, 'title' => 'Летняя кухня', 'tag' => 'Архитектурные проекты', 'area' => '34', 'url' => '#' ),
		array( 'img' => 'project-5.jpg', 'alt' => 'Гостиная студии с мягкой мебелью', 'w' => 903, 'h' => 827, 'title' => 'Студия', 'tag' => 'Интерьеры квартир', 'area' => '27', 'url' => '#' ),
		array( 'img' => 'project-6.jpg', 'alt' => 'Кухня-гостиная с тёплым освещением', 'w' => 904, 'h' => 578, 'title' => 'Однокомнатная квартира', 'tag' => 'Интерьеры квартир', 'area' => '34', 'url' => '#' ),
	);
	/* --- Примеры реализации ---------------------------------------
	   Берём настоящие записи проектов, а не список в шаблоне: это одна
	   и та же сущность, просто показанная иначе, чем на главной. Тогда
	   карточки ведут на страницы проектов, а новый проект появляется
	   здесь сам, без правки кода.

	   Список в шаблоне остаётся запасным: пока проектов нет (свежая
	   установка, пустая база), раздел не должен превращаться в пустоту.
	   ------------------------------------------------------------- */
	$melo_works = get_posts( array(
		'post_type'      => 'projects',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
	) );

	if ( $melo_works ) {
		$projects = array();
		foreach ( $melo_works as $melo_work ) {
			$melo_img = get_the_post_thumbnail_url( $melo_work->ID, 'project_front_big' );
			if ( ! $melo_img ) {
				$melo_img = get_the_post_thumbnail_url( $melo_work->ID, 'full' );
			}

			$projects[] = array(
				/* Полный адрес, а не имя файла: картинка приходит из медиатеки,
				   а не из папки темы. */
				'src'   => $melo_img,
				'alt'   => get_the_title( $melo_work->ID ),
				'w'     => 903,
				'h'     => 1004,
				'title' => get_the_title( $melo_work->ID ),
				'tag'   => (string) get_field( 'type', $melo_work->ID ),
				'area'  => (string) get_field( 'square', $melo_work->ID ),
				'url'   => get_permalink( $melo_work->ID ),
			);
		}
	}
	?>

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

	<!-- ============ 1. Интро ============ -->
	<section class="melo-hero">
		<?php melo_hero_image( get_post_thumbnail_id(), 'hero.jpg' ); ?>
		<div class="melo-hero__veil"></div>

		<div class="melo-container melo-hero__inner">
			<?php
			/* Крошки перенесены сюда из-под шапки по решению заказчика: над
			   заголовком, белым по фотографии. Видимый блок темы при этом
			   отключён в template-parts/content-breadcrumbs.php, чтобы на
			   странице не оказалось двух наборов крошек подряд — второй
			   читался бы скринридером как повтор.
			
			   Плагин Breadcrumb NavXT даёт только <li>, обёртку пишем свою. */
			if ( function_exists( "bcn_display_list" ) ) : ?>
			<nav class="melo-hero__crumbs" aria-label="Хлебные крошки">
				<ul class="melo-breadcrumbs">
					<?php bcn_display_list(); ?>
				</ul>
			</nav>
			<?php endif; ?>

			<h1><?php the_title(); ?></h1>

			<p class="melo-hero__lead"><?php echo esc_html( $lead ); ?></p>

			<?php /* класс open-modal-os и data-title — триггер попапа темы */ ?>
			<button class="melo-btn melo-btn--gold open-modal-os" type="button"
				data-title="<?php echo esc_attr( 'Заказать услугу ' . get_the_title() ); ?>">
				Обсудить проект
			</button>
		</div>
	</section>

	<!-- ============ 2. Что входит в услугу ============ -->
	<section class="melo-section melo-section--gray" id="melo-what">
		<div class="melo-container">
			<div class="melo-section__head melo-section__head--solo" data-reveal>
				<h2><?php echo esc_html( $service_title ); ?></h2>
			</div>

			<div class="melo-service">
				<ul class="melo-service__list">
					<?php foreach ( $service_items as $item ) : ?>
						<li class="melo-service__item" data-reveal>
							<span class="melo-service__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24"><use href="#<?php echo esc_attr( $item['icon'] ); ?>"></use></svg>
							</span>
							<div>
								<h3 class="melo-service__title"><?php echo esc_html( $item['title'] ); ?></h3>
								<p class="melo-service__text"><?php echo esc_html( $item['text'] ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php
				/* Картинка идёт после списка и в разметке: на широких экранах
				   встаёт справа, ниже 860px поднимается под заголовок средствами
				   CSS. Порядок чтения при этом не меняется. */
				?>
				<div class="melo-service__media" data-reveal>
					<img src="<?php echo esc_url( $service_image_src ); ?>"
						alt="<?php echo esc_attr( $service_image_alt ); ?>"
						width="590" height="409" loading="lazy">
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 3. Как мы работаем ============ -->
	<section class="melo-section melo-section--gray" id="melo-steps">
		<div class="melo-container">
			<div class="melo-section__head melo-section__head--center" data-reveal>
				<span class="melo-eyebrow"><?php echo esc_html( $steps_eyebrow ); ?></span>
				<h2><?php echo esc_html( $steps_title ); ?></h2>
			</div>

			<ol class="melo-flow">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="melo-flow__item" data-reveal>
						<span class="melo-flow__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
						<h3 class="melo-flow__title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="melo-flow__text"><?php echo esc_html( $step['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<!-- ============ 4. Примеры реализации ============ -->
	<section class="melo-section melo-section--gray" id="melo-works">
		<div class="melo-container">
			<div class="melo-section__head melo-section__head--center" data-reveal>
				<span class="melo-eyebrow"><?php echo esc_html( $works_eyebrow ); ?></span>
				<h2><?php echo esc_html( $works_title ); ?></h2>
			</div>

			<div class="melo-works-grid">
				<?php foreach ( $projects as $i => $project ) : ?>
					<article class="melo-project" data-reveal>
						<?php
						/* Картинка ведёт туда же, что и заголовок, но скрыта от
						   скринридера и от табуляции: иначе одна и та же ссылка
						   читалась бы дважды подряд. */
						?>
						<a class="melo-project__media" href="<?php echo esc_url( $project['url'] ); ?>" tabindex="-1" aria-hidden="true">
							<?php /* у проектов из базы адрес полный, у запасного списка — имя файла */ ?>
							<img src="<?php echo esc_url( isset( $project['src'] ) ? $project['src'] : $melo_dir . $project['img'] ); ?>"
								alt="<?php echo esc_attr( $project['alt'] ); ?>"
								width="<?php echo esc_attr( $project['w'] ); ?>"
								height="<?php echo esc_attr( $project['h'] ); ?>" loading="lazy">
						</a>
						<div class="melo-project__meta">
							<p>
								<span class="melo-project__num"><?php echo esc_html( sprintf( '%02d.', $i + 1 ) ); ?></span><a class="melo-project__title" href="<?php echo esc_url( $project['url'] ); ?>"><?php echo esc_html( $project['title'] ); ?></a>
							</p>
							<span class="melo-project__area"><?php echo esc_html( $project['area'] ); ?> <sup>м2</sup></span>
							<p class="melo-project__tag"><?php echo esc_html( $project['tag'] ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="melo-works__cta">
				<a class="melo-btn melo-btn--navy melo-btn--wide" href="<?php echo esc_url( home_url( '/projects/' ) ); ?>">
					Все проекты
					<svg class="melo-btn__arrow" aria-hidden="true"><use href="#melo-i-arrow"></use></svg>
				</a>
			</div>
		</div>
	</section>

	<?php
	// Если в редакторе страницы что-то набрано — выводим под секциями.
	$melo_content = get_the_content();
	if ( trim( wp_strip_all_tags( $melo_content ) ) ) :
		?>
		<section class="melo-section melo-section--white">
			<div class="melo-container entry-content"><?php the_content(); ?></div>
		</section>
		<?php
	endif;
	?>


</div><!-- /.melo-page -->

	<?php
endwhile;

get_footer();
