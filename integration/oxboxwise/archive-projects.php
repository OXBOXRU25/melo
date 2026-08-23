<?php
/**
 * ВНИМАНИЕ: файл темы, заменённый пакетом MELO.
 *
 * Отличие одно: над списком появилась панель направлений. Всё остальное
 * — как в теме.
 *
 * Панель собрана на классах темы (.filter, .filter__item-btn) — те же,
 * которыми на странице проекта выводятся чипы с типом и параметрами.
 * Так каталог и карточка говорят на одном языке, и нам не приходится
 * рисовать вторую кнопку-пилюлю с нуля.
 *
 * Кнопки — настоящие ссылки на адрес направления. Без JS они работают
 * как обычная навигация, с JS переключение происходит на месте (см.
 * js/melo-page.js). Направления, в которых пока нет ни одного проекта,
 * выводятся приглушёнными и не кликаются: пустой каталог в ответ на
 * нажатие читается как поломка.
 *
 * @package oxboxwise
 */

get_header();
?>



		<?php if ( have_posts() ) : ?>

<div class="bg-gray">
<h1 class="title-page title-page_mb title title_white container"><?=get_the_archive_title();?></h1>

			<?php
			$melo_dirs = function_exists( 'melo_directions' ) ? melo_directions() : array();

			if ( $melo_dirs ) :
				/* Сколько проектов в каждом направлении — чтобы отличить живые
				   кнопки от пустых. Один запрос на все термины. */
				$melo_terms = get_terms(
					array(
						'taxonomy'   => 'napravlenie',
						'hide_empty' => false,
					)
				);
				$melo_counts = array();
				if ( ! is_wp_error( $melo_terms ) ) {
					foreach ( $melo_terms as $melo_term ) {
						$melo_counts[ $melo_term->slug ] = (int) $melo_term->count;
					}
				}

				$melo_current = get_query_var( 'napravlenie' );
				?>
				<div class="filter melo-filter" data-melo-filter="<?php echo $melo_current ? 'term' : 'all'; ?>">
					<ul class="filter__list container">
						<li class="filter__item">
							<a class="filter__item-btn<?php echo $melo_current ? '' : ' is-active'; ?>"
								href="<?php echo esc_url( get_post_type_archive_link( 'projects' ) ); ?>"
								data-melo-dir="">Все</a>
						</li>

						<?php foreach ( $melo_dirs as $melo_slug => $melo_label ) : ?>
							<?php if ( empty( $melo_counts[ $melo_slug ] ) ) : ?>
								<?php /* проектов нет — показываем, но не даём нажать */ ?>
								<li class="filter__item">
									<span class="filter__item-btn is-empty" aria-disabled="true"><?php echo esc_html( $melo_label ); ?></span>
								</li>
							<?php else : ?>
								<li class="filter__item">
									<a class="filter__item-btn<?php echo $melo_current === $melo_slug ? ' is-active' : ''; ?>"
										href="<?php echo esc_url( get_term_link( $melo_slug, 'napravlenie' ) ); ?>"
										data-melo-dir="<?php echo esc_attr( $melo_slug ); ?>"><?php echo esc_html( $melo_label ); ?></a>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

            <section class="project project_mb">
                <div class="project__inner container">
                    <ul class="project__list" data-melo-filter-list>


						<?php $counter=1; while ( have_posts() ) : the_post();?>


							<? get_template_part( 'template-parts/content', get_post_type(), array('counter' => $counter) );?>

						<? $counter++; endwhile;?>




                    </ul>

					<?php /* Показывается скриптом, когда в выбранном направлении
					         не осталось ни одного проекта. */ ?>
					<p class="melo-filter-empty" data-melo-filter-empty hidden>В этом направлении пока нет проектов.</p>
                </div>
            </section>


	</div>





		<? else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>



<?php
get_footer();
