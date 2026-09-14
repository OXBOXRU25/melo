<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package oxboxwise
 */



if($args['counter']==1 or $args['counter']==4 or $args['counter']==7 or $args['counter']==10  or $args['counter']==13  or $args['counter']==16  or $args['counter']==19  or $args['counter']==22  or $args['counter']==25)
    $class='project__item_size-big';
elseif($args['counter']==5 or $args['counter']==11  or $args['counter']==17   or $args['counter']==23  or $args['counter']==26  or $args['counter']==29)
    $class='project__item_size-middle';
elseif($args['counter']==2 or $args['counter']==3 or $args['counter']==6 or $args['counter']==8 or $args['counter']==9 or $args['counter']==12 or $args['counter']==14 or $args['counter']==15 or $args['counter']==18 or $args['counter']==20 or $args['counter']==21 or $args['counter']==24) 
    $class="";
else
	$class="";

?>


<li
								class="project__item <? echo $class ?> wow animate__animated animate__fadeInUp animate__delay-05s"
								<?php /* направления слагами — по ним фильтрует панель над каталогом */ ?>
								data-melo-dir="<?php echo esc_attr( function_exists( 'melo_post_directions' ) ? melo_post_directions() : '' ); ?>">
								<a href="<? echo get_permalink(); ?>">
								  <picture class="project__item-picture">
									<? if($class=="project__item_size-big"): ?>
									<img src="<? echo esc_url( melo_project_image( get_the_ID(), 'project_front_big' ) ); ?>" alt="<? the_title(); ?>" loading="lazy" decoding="async">
									<? endif; ?>
									<? if($class=="project__item_size-middle"): ?>
									<img src="<? echo esc_url( melo_project_image( get_the_ID(), 'project_front_middle' ) ); ?>" alt="<? the_title(); ?>" loading="lazy" decoding="async">
									<? endif; ?>
									<? if($class==""): ?>
									<img src="<? echo esc_url( melo_project_image( get_the_ID(), 'project_front_small' ) ); ?>" alt="<? the_title(); ?>" loading="lazy" decoding="async">
									<? endif; ?>
									  
								  </picture>
								  <div class="project__item-content">
									<div class="project__item-box">
									  <p class="project__item-name">
										<sup><? if($args['counter']<10): ?>00<? elseif($args['counter']<100): ?>0<? endif; ?><? echo $args['counter']; ?>. </sup><? the_title(); ?>
									  </p>
									  <p class="project__item-description"><? echo get_field('type'); ?></p>
									</div>
									<p class="project__item-size">
									  <? echo get_field('square'); ?> <sup>м2</sup>
									</p>
								  </div>
								</a>
							  </li>


