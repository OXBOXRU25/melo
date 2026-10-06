<?php
/*
Plugin Name: Wisedev Plugin
Plugin URI: https://www.damiencarbery.com/2018/11/remove-type-from-script-and-style-markup/
Description: Remove 'type="text/javascript"' from 'script' tags and 'type="text/css"' from 'style' tags.
Author: Damien Carbery
Author URI: https://www.damiencarbery.com
Version: 0.2
*/


add_filter( 'script_loader_tag', 'dcwd_remove_type', 10, 3 );
add_filter( 'style_loader_tag', 'dcwd_remove_type', 10, 3 );  // Ignore the $media argument to allow for a common function.
function dcwd_remove_type( $markup, $handle, $href ) {
    //error_log( 'Markup: ' . $markup );
    //error_log( 'Handle: ' . $handle );
    //error_log( 'Href: ' . $href );

    // Remove the 'type' attribute.
    $markup = str_replace( " type='application/javascript'", '', $markup );
    $markup = str_replace( ' type="application/javascript"', '', $markup );
    $markup = str_replace( " type='text/javascript'", '', $markup );
    $markup = str_replace( "type='text/javascript'", ' ', $markup );
    $markup = str_replace( " type='text/css'", '', $markup );

    return $markup;
}


// Store and process wp_head output to operate on inline scripts and styles.
add_action( 'wp_head', 'dcwd_wp_head_ob_start', 0 );
function dcwd_wp_head_ob_start() {
    ob_start();
}


add_action( 'wp_head', 'dcwd_wp_head_ob_end', 10000 );
function dcwd_wp_head_ob_end() {
    $wp_head_markup = ob_get_contents();
    ob_end_clean();

    // Remove the 'type' attribute. Note the use of single and double quotes.
    $wp_head_markup = str_replace( " type='text/javascript'", '', $wp_head_markup );
    $wp_head_markup = str_replace( ' type="text/javascript"', '', $wp_head_markup );
    $wp_head_markup = str_replace( ' type="text/css"', '', $wp_head_markup );
    $wp_head_markup = str_replace( " type='text/css'", '', $wp_head_markup );

    echo $wp_head_markup;
}

/**
 * Установка HTTP заголовка Last-Modified
 * При активации плагина, у всех незапароленных постов в HTTP-заголовках появится Last-Modified
 * https://sheensay.ru/?p=247
 */

add_action('template_redirect', '_returnNotModified');
function _returnNotModified($headers)
{
    if( is_singular() ) {
        $post_id = get_queried_object_id();
        $LastModified = gmdate("D, d M Y H:i:s \G\M\T", $post_id);
        $LastModified_unix = gmdate("D, d M Y H:i:s \G\M\T", $post_id);
        $IfModifiedSince = false;
        if( $post_id ) {
            if (isset($_ENV['HTTP_IF_MODIFIED_SINCE']))
                $IfModifiedSince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
                $IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
            if ($IfModifiedSince && $IfModifiedSince >= $LastModified_unix) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
                exit;
            }
            header("Last-Modified: " . get_the_modified_time("D, d M Y H:i:s", $post_id) );
        }
    }
}