<?php
/**
 * Template Name: Главная обновленная
 * Template Post Type: page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package oxboxwise
 */

/*
 * Главная использует первые два блока новой стилистики MELO, поэтому
 * подключается тем же вызовом, что и остальные страницы MELO: стили
 * содержимого плюс запуск появления блоков. До get_header(), чтобы CSS
 * успел в wp_head().
 *
 * Раньше здесь стояли свои wp_enqueue_* — копия того, что уже делают
 * melo_enqueue_chrome() и melo_enqueue_page(). Шрифты из-за этого
 * запрашивались дважды под разными именами, а запуск появления жил
 * только на главной: на остальных страницах MELO блоки не анимировались
 * вовсе.
 */
melo_enqueue_page();

$melo_dir = get_template_directory_uri() . '/img/melo/';

/* Первый экран сохраняет реальные поля главной, меняется только дизайн. */
$hero_image = get_field( 'intro_image' );

/* Идентификатор нужен, чтобы srcset собрал сам WordPress: он уже нарезал
   этот файл на девятнадцать размеров, грех не воспользоваться. */
$melo_hero_id = is_array( $hero_image ) ? (int) ( $hero_image['ID'] ?? 0 ) : (int) $hero_image;
$hero_bg    = '';

if ( is_array( $hero_image ) && ! empty( $hero_image['url'] ) ) {
    $hero_bg = $hero_image['url'];
} elseif ( is_numeric( $hero_image ) ) {
    $hero_bg = wp_get_attachment_image_url( (int) $hero_image, 'full' );
} elseif ( is_string( $hero_image ) ) {
    $hero_bg = $hero_image;
}

if ( ! $hero_bg ) {
    $hero_bg = get_the_post_thumbnail_url( get_the_ID(), 'full' );
}
if ( ! $hero_bg ) {
    $hero_bg = $melo_dir . 'hero.jpg';
}

$hero_title = get_field( 'intro_title' );
if ( ! $hero_title ) {
    $hero_title = get_the_title();
}

$hero_lead = get_field( 'intro_text' );
if ( ! $hero_lead ) {
    $hero_lead = get_the_excerpt();
}

/* Карточки перенесены из шаблона «MELO — направление деятельности». */
$cards_title = 'Направления деятельности';

/* Куда ведёт карточка. Пока страница услуги одна, поэтому адрес общий;
   когда появятся остальные, достаточно проставить свой url в массиве
   $cards. Ищем по слагу, чтобы не зависеть от идентификатора записи. */
$melo_service_page = get_page_by_path( 'melo-arhitekturnoe-proektirovanie' );
$melo_service_url  = $melo_service_page ? get_permalink( $melo_service_page ) : home_url( '/' );
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

$allow_br = array( 'br' => array() );

get_header();
?>


<div class="melo-page melo-home-intro">
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <symbol id="melo-i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 12h15M13 6l6 6-6 6"/>
        </symbol>
    </svg>

    <!-- ============ 1. Интро ============ -->
    <section class="melo-hero">
        <?php melo_hero_image( $melo_hero_id, 'hero.jpg' ); ?>
        <div class="melo-hero__veil"></div>

        <div class="melo-container melo-hero__inner">
            <h1><?php echo wp_kses_post( $hero_title ); ?></h1>

            <?php if ( $hero_lead ) : ?>
                <p class="melo-hero__lead"><?php echo wp_kses_post( $hero_lead ); ?></p>
            <?php endif; ?>

            <button class="melo-btn melo-btn--gold open-modal-os" type="button" data-title="Рассчитать стоимость">
                Рассчитать стоимость
            </button>
        </div>
    </section>

    <!-- ============ 2. Направления деятельности ============ -->
    <section class="melo-section melo-section--gray" id="areas" data-anc_id="#areas">
        <div class="melo-container">
            <div class="melo-section__head" data-reveal>
                <h2 class="melo-eyebrow"><?php echo esc_html( $cards_title ); ?></h2>
            </div>

            <div data-slider>
                <div class="melo-slider-track" data-slider-track>
                    <?php foreach ( $cards as $i => $card ) : ?>
                        <article class="melo-card" data-reveal>
                            <div class="melo-card__media">
                                <?php /* картинка ведёт туда же, что и заголовок, но скрыта
                                   от скринридера и табуляции: иначе одна и та же ссылка
                                   читалась бы дважды подряд */ ?>
                                <a class="melo-card__media-link" href="<?php echo esc_url( isset( $card['url'] ) ? $card['url'] : $melo_service_url ); ?>" tabindex="-1" aria-hidden="true">
                                    <img src="<?php echo esc_url( $melo_dir . $card['img'] ); ?>"
                                        alt="<?php echo esc_attr( $card['alt'] ); ?>"
                                        width="590" height="409" loading="lazy">
                                </a>
                            </div>
                            <div class="melo-card__head">
                                <span class="melo-card__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
                                <h3><a class="melo-card__link" href="<?php echo esc_url( isset( $card['url'] ) ? $card['url'] : $melo_service_url ); ?>"><?php echo wp_kses( $card['title'], $allow_br ); ?></a></h3>
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
</div>

<div class="bg-gray">

		<section class="about" id="about" data-speed="0.97" data-anc_id="#about">

            <div class="container">

              <div class="about__inner">

                <div class="about__left">
                  <h2
                    class="about__section-title title-section title-section_white wow animate__animated animate__fadeInUp animate__delay-05s">
                    О компании
                  </h2>

                  <div class="about__human">

                    <?php /* ниже сгиба — грузим лениво, чтобы не задерживать первую отрисовку */ ?>
                    <img src="<? echo get_field('about_photo')['url']; ?>" alt="" loading="lazy" decoding="async">

                    <p>
                      <? echo get_field('about_name'); ?>
                    </p>
                    <span>
                       <? echo get_field('about_seat'); ?>
                    </span>

                  </div>

                </div>

                <div class="about__right">
                  <h3 class="about__title wow animate__animated animate__fadeInUp animate__delay-05s">
                    <? echo get_field('about_title'); ?>
                  </h3>
                  <p class="about__description wow animate__animated animate__fadeInUp animate__delay-05s">
                    <? echo get_field('about_text'); ?>
                  </p>
                  <ul class="about__benefits">
					  
					  <? foreach(get_field('about_adv') as $adv): ?>
					  
					  <li class="about__benefits-item wow animate__animated animate__fadeInUp animate__delay-05s">

                      <p>
                        <? echo $adv['title'] ?>
                      </p>

                      <p>
                         <? echo $adv['text'] ?>
                      </p>

                    </li>
					
                 
					<? endforeach; ?>
					  
                    
                  </ul>
                </div>

              </div>

            </div>

          </section>
	
	
	
	
          
	
         
        </div>
	
	
	
	
        <section class="project project_p-block" id="projects" data-anc_id="#projects">
          <div class="project__inner container">
            <div class="project__top section-top wow animate__animated animate__fadeInUp animate__delay-05s">
              <h2 class="project__section-title title-section title-section_dark">
                Проекты
              </h2>
              <strong class="project__title title"><? echo get_field('projects_title'); ?></strong>
            </div>
            <ul class="project__list project__list_mb">
				
				<?
					$args = array(
						'posts_per_page' => 6,
						'post_type' => 'projects',
						'post_status' => 'publish',
					);

					$query = new WP_Query( $args );

					// Цикл
					if ( $query->have_posts() ) {
						$counter=1;
						while ( $query->have_posts() ) {
							$query->the_post();
							
						?>
				
				<? get_template_part( 'template-parts/content', get_post_type(), array('counter' => $counter) );?>
							
							
						<?
							$counter++;	
						}
						
					}
					else {
						// Постов не найдено
					}

					// Возвращаем оригинальные данные поста. Сбрасываем $post.
					wp_reset_postdata();
				?>
				
              
             
            </ul>
            <a href="/projects/" class="project__btn btn_animate btn wow animate__animated animate__fadeInUp animate__delay-05s"
              type="button">
              <div class="btn_animate-box">
                <span>Все проекты</span>
                <span>Все проекты</span>
              </div>
              <div class="btn_animate-box-icon">
                <div class="btn_icon">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z"
                      fill="white" />
                  </svg>
                </div>
                <div class="btn_icon">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z"
                      fill="white" />
                  </svg>
                </div>
              </div>
            </a>
          </div>
        </section>
	
	
	
	
	<section class="service melo-service-light service_p-block wow animate__animated animate__slideInUp" id="services"   data-anc_id="#services">
          <div class="service__inner container">

            <div class="service__top section-top wow animate__animated animate__fadeInUp animate__delay-05s">
              <h2 class="service__section-title title-section title-section_onblack">
                Услуги
              </h2>
			
            </div>
			 <div class="service__top section-top wow animate__animated animate__fadeInUp animate__delay-05s">
              
				<div class="subtitle">
					Для коммерческих интерьеров цена рассчитывается индивидуально.
				</div>
            </div>

            <ul class="service__list">
				
				<? $counter=1; foreach(get_field('services') as $service): ?>
				
				
					
					
					
					<li class="service__item wow animate__animated animate__fadeInUp animate__delay-05s">

                <div class="service__item-left">
                  <h3 class="service__item-title title title_onblack">
                    <? echo $service['services_name'] ?>
                  </h3>
                  <p class="service__item-description">
                   <? echo $service['services_subtitle'] ?>
                  </p>
                </div>

                <div class="service__item-box service__item-box_1">
                  <p class="service__item-content">
                   <? echo $service['services_pack'] ?>
                  </p>
                  <button class="service__btn btn btn_outline open-modal-os" type="button"  data-title="Заказать услугу <? echo $service['services_name_2'] ?>">
                    Заказать
                    <div class="btn_icon">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z"
                          fill="white" />
                      </svg>
                  </button>
                </div>
                <p class="service__item-price"><? echo $service['services_price'] ?></p>
                <div class="service__item_decor wow animate__animated animate__fadeInLeft animate__delay-1s"></div>
              </li>
				
				<? $counter++; endforeach; ?>
				

              

              

              

            </ul>
          </div>
        </section>
	
	
	
	
	
        
        
        

        
        


<?php
get_footer();
