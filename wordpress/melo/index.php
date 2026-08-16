<?php
/**
 * Запасной шаблон. WordPress требует его наличия в любой классической теме.
 *
 * @package melo
 */

get_header();
?>

<section class="section section--white">
	<div class="container">

		<?php if ( have_posts() ) : ?>

			<div class="section__head">
				<h1>
					<?php
					if ( is_home() && ! is_front_page() ) {
						single_post_title();
					} elseif ( is_archive() ) {
						the_archive_title();
					} elseif ( is_search() ) {
						printf(
							/* translators: %s — поисковый запрос */
							esc_html__( 'Результаты поиска: %s', 'melo' ),
							esc_html( get_search_query() )
						);
					} else {
						bloginfo( 'name' );
					}
					?>
				</h1>
			</div>

			<div class="works-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="project" data-reveal>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="project__media" href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
							</a>
						<?php endif; ?>

						<div class="project__meta">
							<p><a class="project__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
							<p class="project__tag"><?php echo esc_html( get_the_date() ); ?></p>
						</div>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<div class="works__cta">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => esc_html__( 'Назад', 'melo' ),
						'next_text' => esc_html__( 'Вперёд', 'melo' ),
					)
				);
				?>
			</div>

		<?php else : ?>

			<div class="section__head">
				<h1><?php esc_html_e( 'Ничего не найдено', 'melo' ); ?></h1>
			</div>
			<p class="section__intro"><?php esc_html_e( 'Попробуйте изменить запрос или вернитесь на главную.', 'melo' ); ?></p>
			<a class="btn btn--gold" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'На главную', 'melo' ); ?></a>

		<?php endif; ?>

	</div>
</section>

<?php
get_footer();
