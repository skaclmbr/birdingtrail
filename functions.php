<?php 

function wpbootstrap_scripts_with_jquery()
{
	//wp_enqueue_script('jquery', get_stylesheet_directory_uri() . '/js/jquery-3.3.1.slim.min.js'); //not sure if needed?
	wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/bootstrap/css/bootstrap.css');
	wp_enqueue_style('parent-style', get_stylesheet_directory_uri() . '/style.css',array('bootstrap'));
	//wp_enqueue_style('bootstrap-resp', get_stylesheet_directory_uri() . '/bootstrap/css/bootstrap-responsive.css');

	// Register the script like this for a theme:
	wp_register_script( 'bootstrap-js', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', array( 'jquery' ), '4.0.0', false );
	// For either a plugin or a theme, you can then enqueue the script:
	wp_enqueue_script( 'custom-script' );
}
add_action( 'wp_enqueue_scripts', 'wpbootstrap_scripts_with_jquery' );

function btstarter_wp_setup() {
	add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'btstarter_wp_setup' );

function bt_register_menu() {
	register_nav_menu('header-menu', __( 'Header Menu'));
}
add_action( 'init', 'bt_register_menu' );

function bt_widgets_init() {
    register_sidebar( array(
        'name'          => 'Footer - Copyright Text',
        'id'            => 'footer-copyright-text',
        'before_widget' => '<div class="inner footer_copyright_text">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ) );
 
}
add_action( 'widgets_init', 'bt_widgets_init' );


//Eventually, here is where we add options for Appearance > Customize
// see here: https://www.lyrathemes.com/bootstrap-wordpress-theme-tutorial-1/
// - locate main logo - fix in cover.php

?>