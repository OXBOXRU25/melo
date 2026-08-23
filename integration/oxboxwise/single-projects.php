<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package oxboxwise
 */

get_header();
?>



<div class="top-page top-page_mb container">
                <div class="top-page__box">
                    <h1 class="title-page title_big-size title_dark"><? the_title(); ?></h1>
                    <ul class="filter__list container">
                        <?php
                        /* Чип с полем «Тип» убран по решению заказчика: тип объекта
                           теперь живёт направлениями (таксономия napravlenie), а это
                           поле осталось от прежней схемы и показывало устаревшее
                           значение. Остаются параметры проекта. */
                        ?>
                        <? foreach (get_field('params') as $param): ?>
						<li class="filter__item">
                            <button class="filter__item-btn filter__item-btn_border no-pointer"
                                type="button"><? echo $param['param']; ?></button>
                        </li>
						<? endforeach; ?>
                    </ul>
                </div>
                <a href="#!" class="top-page__link btn btn_animate top-page__link_big open-modal-os" data-title="Заказать проект <? the_title(); ?>">
                    <div class="btn_animate-box">
                        <span>Заказать проект</span>
                        <span>Заказать проект</span>
                    </div>
                    <div class="btn_animate-box-icon">
                        <div class="top-page__link-icon btn_icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z"
                                    fill="white"></path>
                            </svg>
                        </div>
                        <div class="top-page__link-icon btn_icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z"
                                    fill="white"></path>
                            </svg>
                        </div>
                    </div>

                </a>
            </div>
<? if(get_field('main_image')): ?>
            <div class="full-image full-image_mb" style="background-image: url(<? echo get_field('main_image')['sizes']['project_intro']; ?>);"></div>
<? endif; ?>
            <div class="new-project container">
                <div class="new-project__content">
                    <h2 class="new-project__content-title">О проекте</h2>
                    <div class="new-project__content-info">
                        <p>
                            <? echo get_field('описание'); ?>
                        </p>
                        <ul class="new-project__content-list">
							<? if(get_field('adr')): ?>
                            <li class="new-project__content-item">
                                <p class="new-project__content-item-name">Адрес</p>
                                <p class="new-project__content-item-info"><? echo get_field('adr'); ?></p>
                            </li>
							<? endif; ?>
							<? if(get_field('square')): ?>
                            <li class="new-project__content-item">
                                <p class="new-project__content-item-name">Площадь</p>
                                <p class="new-project__content-item-info"><? echo get_field('square'); ?> м²</p>
                            </li>							
							<? endif; ?>
							<? if(get_field('price')): ?>
                            <li class="new-project__content-item">
                                <p class="new-project__content-item-name">Стоимость реализации</p>
                                <p class="new-project__content-item-info"><? echo get_field('price'); ?> ₽</p>
                            </li>							
							<? endif; ?>
							<? if(get_field('time')): ?>
                            <li class="new-project__content-item">
                                <p class="new-project__content-item-name">Срок реализации</p>
                                <p class="new-project__content-item-info"><? echo get_field('time'); ?></p>
                            </li>
							<? endif; ?>
                        </ul>
                    </div>
                </div>
				
				<? foreach(get_field('desc_photo') as $desc_photo): ?>
                <div class="new-project__content">
					<? if($desc_photo['title']): ?>
                    <h2 class="new-project__content-title"><? echo $desc_photo['title'] ?></h2>
					<? endif; ?>
                    <div class="new-project__content-info">
						<? if($desc_photo['photo']): ?>
                        <picture class="new-project__content-picture">
                            <img src="<? echo $desc_photo['photo']['sizes']['project_desc'] ?>" alt="">
                        </picture>
						<? endif; ?>
						<? if($desc_photo['text']): ?>
                        <p>
                            <? echo $desc_photo['text'] ?>
                        </p>
						<? endif; ?>
						<? if($desc_photo['iframe']): ?>
                        <p>
                            <? echo $desc_photo['iframe'] ?>
                        </p>
						<? endif; ?>
                    </div>
                </div>
				<? endforeach; ?>
				
               
            </div>
			<? if(get_field('gallery')): ?>
            <section class="new-project-gallery container">
                <h2 class="new-project-gallery__title title title_dark">Галерея</h2>
                <div class="new-project-gallery__inner">
					<? foreach(get_field('gallery') as $gallery): ?>
					<a href="<? echo $gallery['sizes']['project_gallery_popup'] ?>" data-fancybox="gallery">
						<picture class="new-project-gallery__picture">
							<img src="<? echo $gallery['sizes']['project_gallery'] ?>" alt="">
						</picture>
					</a>
					<? endforeach; ?>
                    
                </div>
            </section>
			<? endif; ?>

			<?php
			/* Кнопка заказа перенесена сюда из верхнего блока по решению
			   заказчика: предлагать заказ логичнее после того, как человек
			   посмотрел галерею, а не до того, как увидел хоть одно фото.
			   Обёртка своя — у ссылки классы .top-page__*, рассчитанные на
			   строку с заголовком, ширину и выравнивание задаём заново. */
			?>
			<div class="melo-project-cta container">
				<a href="#!" class="top-page__link btn btn_animate top-page__link_big open-modal-os" data-title="Заказать проект <? the_title(); ?>">
				    <div class="btn_animate-box">
				        <span>Заказать проект</span>
				        <span>Заказать проект</span>
				    </div>
				    <div class="btn_animate-box-icon">
				        <div class="top-page__link-icon btn_icon">
				            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
				                xmlns="http://www.w3.org/2000/svg">
				                <path
				                    d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z"
				                    fill="white"></path>
				            </svg>
				        </div>
				        <div class="top-page__link-icon btn_icon">
				            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
				                xmlns="http://www.w3.org/2000/svg">
				                <path
				                    d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z"
				                    fill="white"></path>
				            </svg>
				        </div>
				    </div>

				</a>
			</div>

			



<?php
get_footer();
