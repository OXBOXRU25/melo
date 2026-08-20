<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package oxboxwise
 */

?>
    <!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		 <meta name="format-detection" content="telephone=no">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"
    integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="<?=get_template_directory_uri()?>/js/gsap/gsap.min.js" defer></script>
  <script src="<?=get_template_directory_uri()?>/js/gsap/ScrollSmoother.min.js" defer></script>
  <script src="<?=get_template_directory_uri()?>/js/gsap/ScrollTrigger.min.js" defer></script>
  <script defer src="<?=get_template_directory_uri()?>/js/jquery-3.7.1.min.js"></script>
  <script defer src="<?=get_template_directory_uri()?>/js/jquery.mask.min.js"></script>
  <script defer src="<?=get_template_directory_uri()?>/js/jquery.validate.min.js"></script>
		<script defer src="<?=get_template_directory_uri()?>/js/libs.min.js"></script>
  <script defer src="<?=get_template_directory_uri()?>/js/validation.js?v=<?php echo melo_asset_ver( '/js/validation.js' ); ?>"></script>
		<script defer src="<?=get_template_directory_uri()?>/js/custom.js?v=<?php echo melo_asset_ver( '/js/custom.js' ); ?>"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  <script src="<?=get_template_directory_uri()?>/js/main.min.js" defer></script>
  <script src="<?=get_template_directory_uri()?>/js/index.js" type="module"></script>
  <link rel="stylesheet" href="<?=get_template_directory_uri()?>/css/style.min.css?v=<?php echo melo_asset_ver( '/css/style.min.css' ); ?>">
  <link rel="stylesheet" href="<?=get_template_directory_uri()?>/css/media.min.css">
		<link rel="stylesheet" href="<?=get_template_directory_uri()?>/css/custom.css?v=<?php echo melo_asset_ver( '/css/custom.css' ); ?>">

        <?php wp_head(); ?>
    </head>

	
	<? 
		if(get_queried_object()->name=='projects')
			$class="bg-gray"
	?>
	
<body <?php body_class($class); ?>>
<div class="wrapper">
    
<? get_template_part( 'template-parts/content', 'header' );?>
	
      
        
<?php if ((is_front_page() || is_home())) {} else {
    get_template_part( 'template-parts/content', 'breadcrumbs' );
} ?>