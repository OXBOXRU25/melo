<?php
/**
 * Template Name: MELO — направление деятельности
 * Template Post Type: page
 *
 * Внутренняя страница направления в новой стилистике MELO: интро с матовым
 * стеклом, «Направления деятельности» слайдером карточек, «Как мы работаем»,
 * «Примеры реализации».
 *
 * Отличается от «MELO — страница услуги» только вторым блоком: там список
 * позиций с иконками и одной картинкой, здесь слайдер карточек с фото.
 *
 * Шапка и подвал — свои, в новой стилистике MELO: страницы идут в новом
 * дизайне, старая шапка темы на них не к месту. Каркас документа лежит
 * в template-parts/melo-header.php и melo-footer.php.
 *
 * Весь контент обёрнут в <div class="melo-page">. Стили в
 * css/melo-page.css заскоуплены под этот класс и наружу не выходят.
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
		$hero_bg = $melo_dir . 'hero.jpg';
	}

	$lead = get_the_excerpt();
	if ( ! $lead ) {
		$lead = 'Создаём цельный образ дома — от планировки комнат до фасада и входной группы. Считаем каждый метр, каждый материал и каждый рубль бюджета.';
	}

	/* ---------------------------------------------------------------
	 * Направления деятельности — карточки слайдера
	 * В заголовке разрешён только <br> — им ловится перенос с макета.
	 * ------------------------------------------------------------- */
	$cards_title = 'Направления деятельности';

	$cards = array(
		array(
			'img'   => 'dir-1.jpg',
			'alt'   => 'Архитектурный макет и рабочие чертежи здания',
			'title' => 'Архитектурное проектирование<br>и планирование',
			'lead'  => 'Сюда входят услуги по:',
			'items' => array(
				'Консультация дизайнера по подбору недвижимости (помощь в выборе правильного «исходника» участка).',
				'Проектирование домов (архитектурный раздел).',
				'Планирование участка и ситуационный план.',
			),
		),
		array(
			'img'   => 'dir-3.jpg',
			'alt'   => 'Фасад современного загородного дома',
			'title' => 'Дизайн интерьера и экстерьера',
			'lead'  => 'Всё, что касается визуального облика и стиля самого здания.',
			'items' => array(
				'Дизайн-проект интерьера дома.',
				'Дизайн экстерьера (фасады, заборы, входные группы).',
			),
		),
		array(
			'img'   => 'dir-2.jpg',
			'alt'   => 'Благоустроенная территория вокруг загородного дома',
			'title' => 'Ландшафт и малые архитектурные<br>формы (МАФ)',
			'lead'  => 'Благоустройство территории вокруг дома.',
			'items' => array(
				'Ландшафтная архитектура (генплан, зонирование).',
				'Проектирование малых сооружений (бани, беседки, навесы, бассейны, зоны отдыха).',
			),
		),
		array(
			'img'   => 'project-6.jpg',
			'alt'   => 'Готовая кухня-гостиная после реализации проекта',
			'title' => 'Реализация и строительство',
			'lead'  => 'Переход от чертежей к физическому воплощению.',
			'items' => array(
				'Строительство и ремонт (реализация объекта под ключ).',
				'Комплектация объектов (подбор мебели, материалов, оборудования).',
			),
		),
		array(
			'img'   => 'project-2.jpg',
			'alt'   => 'Интерьер гостиной, сданной под ключ',
			'title' => 'Сопровождение и управление',
			'lead'  => 'Сервис, который снимает головную боль с заказчика.',
			'items' => array(
				'Управление объектом (комплексное сопровождение всех процессов).',
				'Авторский надзор (обычно идёт в связке с управлением).',
			),
		),
	);

	$steps = array(
		array( 'title' => 'Замер и планировка', 'text' => 'Выезжаем на объект, снимаем размеры и готовим варианты планировочных решений.' ),
		array( 'title' => 'Концепция', 'text' => 'Собираем стилистику, палитру и материалы — по каждому помещению и по фасаду.' ),
		array( 'title' => 'Визуализация', 'text' => 'Показываем фотореалистичный результат до начала работ и правим, пока это бесплатно.' ),
		array( 'title' => 'Чертежи и надзор', 'text' => 'Комплект документации для бригады, комплектация объекта и контроль до сдачи.' ),
	);

	$projects = array(
		array( 'img' => 'project-1.jpg', 'alt' => 'Интерьер квартиры-студии в светлых тонах', 'w' => 903, 'h' => 1004, 'title' => 'Квартира-студия', 'tag' => 'Интерьеры квартир', 'area' => '27', 'url' => '#' ),
		array( 'img' => 'project-2.jpg', 'alt' => 'Гостиная однокомнатной квартиры с каменной стеной', 'w' => 904, 'h' => 644, 'title' => 'Однокомнатная квартира', 'tag' => 'Интерьеры квартир', 'area' => '34', 'url' => '#' ),
		array( 'img' => 'project-3.jpg', 'alt' => 'Интерьер гостиной загородного дома', 'w' => 903, 'h' => 746, 'title' => 'Загородный дом', 'tag' => 'Интерьер загородного дома', 'area' => '70', 'url' => '#' ),
		array( 'img' => 'project-4.jpg', 'alt' => 'Летняя кухня с панорамными окнами', 'w' => 904, 'h' => 1004, 'title' => 'Летняя кухня', 'tag' => 'Архитектурные проекты', 'area' => '34', 'url' => '#' ),
		array( 'img' => 'project-5.jpg', 'alt' => 'Гостиная студии с мягкой мебелью', 'w' => 903, 'h' => 827, 'title' => 'Студия', 'tag' => 'Интерьеры квартир', 'area' => '27', 'url' => '#' ),
		array( 'img' => 'project-6.jpg', 'alt' => 'Кухня-гостиная с тёплым освещением', 'w' => 904, 'h' => 578, 'title' => 'Однокомнатная квартира', 'tag' => 'Интерьеры квартир', 'area' => '34', 'url' => '#' ),
	);

	$allow_br = array( 'br' => array() );
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
			/* Хлебные крошки выводит сама тема, выше по странице (см. header.php).
			   Если нужны внутри первого экрана — раскомментируйте блок ниже
			   и отключите вывод темы для этого шаблона.

			<nav aria-label="Хлебные крошки">
				<ol class="melo-breadcrumbs">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a></li>
					<li><span aria-current="page"><?php the_title(); ?></span></li>
				</ol>
			</nav>
			*/
			?>

			<h1><?php the_title(); ?></h1>

			<p class="melo-hero__lead"><?php echo esc_html( $lead ); ?></p>

			<?php /* класс open-modal-os и data-title — триггер попапа темы */ ?>
			<button class="melo-btn melo-btn--gold open-modal-os" type="button"
				data-title="<?php echo esc_attr( 'Заказать услугу ' . get_the_title() ); ?>">
				Обсудить проект
			</button>
		</div>
	</section>

	<!-- ============ 2. Направления деятельности ============ -->
	<section class="melo-section melo-section--gray" id="melo-what">
		<div class="melo-container">
			<div class="melo-section__head" data-reveal>
				<h2 class="melo-eyebrow"><?php echo esc_html( $cards_title ); ?></h2>
			</div>

			<div data-slider>
				<div class="melo-slider-track" data-slider-track>
					<?php foreach ( $cards as $i => $card ) : ?>
						<article class="melo-card" data-reveal>
							<div class="melo-card__media">
								<img src="<?php echo esc_url( $melo_dir . $card['img'] ); ?>"
									alt="<?php echo esc_attr( $card['alt'] ); ?>"
									width="590" height="409" loading="lazy">
							</div>
							<div class="melo-card__head">
								<span class="melo-card__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
								<h3><?php echo wp_kses( $card['title'], $allow_br ); ?></h3>
							</div>
							<p class="melo-card__lead"><?php echo esc_html( $card['lead'] ); ?></p>
							<ul class="melo-dash-list">
								<?php foreach ( $card['items'] as $item ) : ?>
									<li><?php echo esc_html( $item ); ?></li>
								<?php endforeach; ?>
							</ul>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="melo-slider-foot">
					<div class="melo-slider-progress"><span class="melo-slider-progress__bar"></span></div>
					<div class="melo-slider-nav">
						<button class="melo-slider-btn" type="button" data-slider-prev aria-label="Предыдущее направление">
							<svg width="24" height="24" aria-hidden="true"><use href="#melo-i-arrow"></use></svg>
						</button>
						<button class="melo-slider-btn" type="button" data-slider-next aria-label="Следующее направление">
							<svg width="24" height="24" aria-hidden="true"><use href="#melo-i-arrow"></use></svg>
						</button>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 3. Как мы работаем ============ -->
	<section class="melo-section melo-section--gray" id="melo-steps">
		<div class="melo-container">
			<div class="melo-section__head melo-section__head--center" data-reveal>
				<span class="melo-eyebrow">Процесс</span>
				<h2>Как мы работаем</h2>
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
				<span class="melo-eyebrow">Проекты</span>
				<h2>Примеры реализации</h2>
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
