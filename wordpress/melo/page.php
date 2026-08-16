<?php
/**
 * Обычная страница без выбранного шаблона.
 *
 * @package melo
 */

get_header();
?>

<section class="section section--white">
	<div class="container">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<div class="section__head">
				<h1><?php the_title(); ?></h1>
			</div>

			<div class="entry-content">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="page-links">',
						'after'  => '</div>',
					)
				);
				?>
			</div>
			<?php
		endwhile;
		?>
	</div>
</section>

<?php
get_footer();
