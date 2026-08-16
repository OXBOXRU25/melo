<?php
/**
 * Template Name: Направление деятельности
 * Template Post Type: page
 *
 * Внутренняя страница направления: интро, что мы делаем, как мы работаем,
 * примеры реализации. Выбирается в редакторе страницы, блок «Атрибуты
 * страницы» → «Шаблон».
 *
 * Содержимое блоков собрано в массивы в начале файла — правится здесь либо
 * заменяется на ACF/произвольный тип записи, циклы вывода при этом не меняются.
 *
 * @package melo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	/* ---------------------------------------------------------------
	 * Интро
	 * Заголовок — название страницы, подзаголовок — её цитата (excerpt),
	 * фон — изображение записи. Всё с запасными значениями.
	 * ------------------------------------------------------------- */
	$hero_bg = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	if ( ! $hero_bg ) {
		$hero_bg = melo_img( 'hero.jpg' );
	}

	$lead = get_the_excerpt();
	if ( ! $lead ) {
		$lead = 'Создаём цельный образ дома — от планировки комнат до фасада и входной группы. Считаем каждый метр, каждый материал и каждый рубль бюджета.';
	}

	// Короткая подпись для хлебных крошек: произвольное поле melo_crumb.
	$crumb = get_post_meta( get_the_ID(), 'melo_crumb', true );
	if ( ! $crumb ) {
		$crumb = get_the_title();
	}

	/* ---------------------------------------------------------------
	 * Что мы делаем — карточки слайдера
	 * ------------------------------------------------------------- */
	$cards = array(
		array(
			'img'   => 'project-2.jpg',
			'alt'   => 'Гостиная с панорамным остеклением и дизайнерским светом',
			'w'     => 904,
			'h'     => 644,
			'title' => 'Дизайн-проект интерьера',
			'text'  => 'Планировка, концепция и подбор материалов. С 3D-визуализацией каждого помещения.',
		),
		array(
			'img'   => 'dir-3.jpg',
			'alt'   => 'Фасад современного загородного дома',
			'w'     => 592,
			'h'     => 410,
			'title' => 'Дизайн экстерьера и фасадов',
			'text'  => 'Облик здания, фасадные решения, входные группы и наружное освещение.',
		),
		array(
			'img'   => 'dir-1.jpg',
			'alt'   => 'Рабочие чертежи и макет здания',
			'w'     => 592,
			'h'     => 410,
			'title' => 'Документация и сопровождение',
			'text'  => 'Рабочие чертежи, сметы, комплектация объекта и авторский надзор до сдачи.',
		),
	);

	/* ---------------------------------------------------------------
	 * Как мы работаем — этапы
	 * ------------------------------------------------------------- */
	$steps = array(
		array(
			'title' => 'Замер и планировка',
			'text'  => 'Выезжаем на объект, снимаем размеры и готовим варианты планировочных решений.',
		),
		array(
			'title' => 'Концепция',
			'text'  => 'Собираем стилистику, палитру и материалы — по каждому помещению и по фасаду.',
		),
		array(
			'title' => 'Визуализация',
			'text'  => 'Показываем фотореалистичный результат до начала работ и правим, пока это бесплатно.',
		),
		array(
			'title' => 'Чертежи и надзор',
			'text'  => 'Комплект документации для бригады, комплектация объекта и контроль до сдачи.',
		),
	);

	/* ---------------------------------------------------------------
	 * Примеры реализации
	 * Чтобы тянуть из произвольного типа записи, замените массив на
	 * WP_Query и оставьте разметку цикла как есть.
	 * ------------------------------------------------------------- */
	$projects = array(
		array(
			'img'   => 'project-1.jpg',
			'alt'   => 'Интерьер квартиры-студии в светлых тонах',
			'w'     => 903,
			'h'     => 1004,
			'title' => 'Квартира-студия',
			'tag'   => 'Интерьеры квартир',
			'area'  => '27',
			'url'   => '#',
		),
		array(
			'img'   => 'project-2.jpg',
			'alt'   => 'Гостиная однокомнатной квартиры с каменной стеной',
			'w'     => 904,
			'h'     => 644,
			'title' => 'Однокомнатная квартира',
			'tag'   => 'Интерьеры квартир',
			'area'  => '34',
			'url'   => '#',
		),
		array(
			'img'   => 'project-3.jpg',
			'alt'   => 'Интерьер гостиной загородного дома',
			'w'     => 903,
			'h'     => 746,
			'title' => 'Загородный дом',
			'tag'   => 'Интерьер загородного дома',
			'area'  => '70',
			'url'   => '#',
		),
		array(
			'img'   => 'project-4.jpg',
			'alt'   => 'Летняя кухня с панорамными окнами',
			'w'     => 904,
			'h'     => 1004,
			'title' => 'Летняя кухня',
			'tag'   => 'Архитектурные проекты',
			'area'  => '34',
			'url'   => '#',
		),
		array(
			'img'   => 'project-5.jpg',
			'alt'   => 'Гостиная студии с мягкой мебелью',
			'w'     => 903,
			'h'     => 827,
			'title' => 'Студия',
			'tag'   => 'Интерьеры квартир',
			'area'  => '27',
			'url'   => '#',
		),
		array(
			'img'   => 'project-6.jpg',
			'alt'   => 'Кухня-гостиная с тёплым освещением',
			'w'     => 904,
			'h'     => 578,
			'title' => 'Однокомнатная квартира',
			'tag'   => 'Интерьеры квартир',
			'area'  => '34',
			'url'   => '#',
		),
	);
	?>

	<!-- ============ 1. Интро ============ -->
	<section class="hero">
		<img class="hero__bg" src="<?php echo esc_url( $hero_bg ); ?>" alt="" width="1920" height="1069">
		<div class="hero__veil"></div>

		<div class="container hero__inner">
			<nav aria-label="<?php esc_attr_e( 'Хлебные крошки', 'melo' ); ?>">
				<ol class="breadcrumbs">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Главная', 'melo' ); ?></a></li>
					<li><span aria-current="page"><?php echo esc_html( $crumb ); ?></span></li>
				</ol>
			</nav>

			<h1><?php the_title(); ?></h1>

			<p class="hero__lead"><?php echo esc_html( $lead ); ?></p>

			<a class="btn btn--gold" href="#contacts"><?php esc_html_e( 'Обсудить проект', 'melo' ); ?></a>
		</div>
	</section>

	<!-- ============ 2. Что мы делаем ============ -->
	<section class="section section--gray" id="what">
		<div class="container">
			<div class="section__head section__head--center" data-reveal>
				<span class="eyebrow"><?php esc_html_e( 'Направления', 'melo' ); ?></span>
				<h2><?php esc_html_e( 'Что мы делаем', 'melo' ); ?></h2>
			</div>

			<div class="slider" data-slider>
				<div class="slider-track" data-slider-track>
					<?php foreach ( $cards as $card ) : ?>
						<article class="card" data-reveal>
							<div class="card__media">
								<img src="<?php echo esc_url( melo_img( $card['img'] ) ); ?>"
									alt="<?php echo esc_attr( $card['alt'] ); ?>"
									width="<?php echo esc_attr( $card['w'] ); ?>"
									height="<?php echo esc_attr( $card['h'] ); ?>" loading="lazy">
							</div>
							<h3 class="card__title"><?php echo esc_html( $card['title'] ); ?></h3>
							<p class="card__text"><?php echo esc_html( $card['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="slider-foot">
					<div class="slider-progress"><span class="slider-progress__bar"></span></div>
					<div class="slider-nav">
						<button class="slider-btn" type="button" data-slider-prev aria-label="<?php esc_attr_e( 'Предыдущее направление', 'melo' ); ?>">
							<svg width="24" height="24" aria-hidden="true"><use href="#i-chev-left"></use></svg>
						</button>
						<button class="slider-btn" type="button" data-slider-next aria-label="<?php esc_attr_e( 'Следующее направление', 'melo' ); ?>">
							<svg width="24" height="24" aria-hidden="true"><use href="#i-chev-right"></use></svg>
						</button>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 3. Как мы работаем ============ -->
	<section class="section section--gray" id="steps">
		<div class="container">
			<div class="section__head section__head--center" data-reveal>
				<span class="eyebrow"><?php esc_html_e( 'Процесс', 'melo' ); ?></span>
				<h2><?php esc_html_e( 'Как мы работаем', 'melo' ); ?></h2>
			</div>

			<ol class="flow">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="flow__item" data-reveal>
						<span class="flow__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
						<h3 class="flow__title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="flow__text"><?php echo esc_html( $step['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<!-- ============ 4. Примеры реализации ============ -->
	<section class="section section--gray" id="works">
		<div class="container">
			<div class="section__head section__head--center" data-reveal>
				<span class="eyebrow"><?php esc_html_e( 'Проекты', 'melo' ); ?></span>
				<h2><?php esc_html_e( 'Примеры реализации', 'melo' ); ?></h2>
			</div>

			<div class="works-grid">
				<?php foreach ( $projects as $i => $project ) : ?>
					<article class="project" data-reveal>
						<a class="project__media" href="<?php echo esc_url( $project['url'] ); ?>">
							<img src="<?php echo esc_url( melo_img( $project['img'] ) ); ?>"
								alt="<?php echo esc_attr( $project['alt'] ); ?>"
								width="<?php echo esc_attr( $project['w'] ); ?>"
								height="<?php echo esc_attr( $project['h'] ); ?>" loading="lazy">
						</a>
						<div class="project__meta">
							<p>
								<span class="project__num"><?php echo esc_html( sprintf( '%02d.', $i + 1 ) ); ?></span>
								<span class="project__title"><?php echo esc_html( $project['title'] ); ?></span>
							</p>
							<span class="project__area"><?php echo esc_html( $project['area'] ); ?> <sup>м2</sup></span>
							<p class="project__tag"><?php echo esc_html( $project['tag'] ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="works__cta">
				<a class="btn btn--navy btn--wide" href="<?php echo esc_url( home_url( '/projects/' ) ); ?>">
					<?php esc_html_e( 'Все проекты', 'melo' ); ?>
					<svg class="btn__arrow" aria-hidden="true"><use href="#i-arrow"></use></svg>
				</a>
			</div>
		</div>
	</section>

	<?php
	// Если в редакторе страницы что-то набрано — выводим под секциями.
	$content = get_the_content();
	if ( trim( wp_strip_all_tags( $content ) ) ) :
		?>
		<section class="section section--white">
			<div class="container entry-content">
				<?php the_content(); ?>
			</div>
		</section>
		<?php
	endif;

endwhile;

get_footer();
