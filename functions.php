<?php 
ini_set( 'mysql.trace_mode', 0); //this mode must be disabled so that blog pagination works

function wpbootstrap_scripts_with_jquery()
{
	global $post;
	wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/bootstrap/css/bootstrap.css');
	wp_enqueue_style('parent-style', get_stylesheet_directory_uri() . '/style.css',array('bootstrap'));
	wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');

	// Register the script like this for a theme:
	wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', array( 'jquery' ), '4.0.0', false );

	//google fonts
	wp_register_style('google_fonts','https://fonts.googleapis.com/css?family=Pacifico',array(),null);
	wp_enqueue_style('google_fonts');
	wp_register_style('google_fonts2','https://fonts.googleapis.com/css?family=Source+Sans+Pro',array(),null);
	wp_enqueue_style('google_fonts2');

	if (is_page() || is_single())
	{
		switch($post->post_name) //post_name is the post slug which is more consistent
		{
			case 'sites':
				//scripts for running the google maps
				wp_enqueue_script('ncbt-google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyC1D62u_pN2PRUT6gCBkfZoZXiVU1F4Vxk&v=3&libraries=places'); //old key, enable place id retrieval
				//NEW API KEY 12/3/18 - AIzaSyDHGo3qFRpz1BGcH6zGcNyqWC6FB3C9Q4g
				//OLD API KEY 12/3/18 - AIzaSyC1D62u_pN2PRUT6gCBkfZoZXiVU1F4Vxk
				wp_enqueue_script('ncbt_map', get_stylesheet_directory_uri() . '/js/map.js');
				wp_enqueue_script('ncbt-what3words', get_stylesheet_directory_uri() . '/js/w3w/dist/W3W.Geocoder.min.js'); //What3Words Javascript wrapper

				break;
			case 'blog': //this doesn't seem to be triggered - CONSIDER REMOVING
				wp_enqueue_style('blog-css', get_stylesheet_directory_uri() . '/css/blog.css');
				break;
		}
	}
}
add_action( 'wp_enqueue_scripts', 'wpbootstrap_scripts_with_jquery' );

//==============================================================================
//This allows use of svg files
function add_file_types_to_uploads($file_types){
	$new_filetypes = array();
	$new_filetypes['svg'] = 'image/svg+xml';
	$file_types = array_merge($file_types, $new_filetypes );
	return $file_types;
}
add_action('upload_mimes', 'add_file_types_to_uploads');


//==============================================================================
//These functions enable a direct URL to a site on the map

// add `siteslug` to query vars
// enables the following to pass query variable: ncbirdingtrail/sites/?site=airlie-gardens
// NOTE - this adds a custom query variable, the redirection plugin allows for clean URIs to point to this query URI version
//		example: ncbirdingtrail.org/sites/airlie-gardens => ncbirdingtrail.org/sites/?site=airlie-gardens

add_filter( 'init', 'add_site_query_var' );
function add_site_query_var()
{
    global $wp;
    $wp->add_query_var( 'site' );
}


//==============================================================================
//These functions feed NCBT or BFB data to a map page

//FUTURE: add table creation/functionality to WP database (rather than separately)
//  - https://deliciousbrains.com/creating-custom-table-php-wordpress/


//Adds ajaxurl variable to JS on pages, used in ajax calls
add_action('wp_head','pluginname_ajaxurl');
function pluginname_ajaxurl() {
	?>
	<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script>
	<?php
}

// ======================================================================================
// add customization for theme

//enables the featured image functionality on blog posts

if (function_exists( 'add_theme_support')) {
	add_theme_support('post-thumbnails');

	//set_post_thumbnail_size(480, 340 ,true); //4:3 ratio, crop mode
	set_post_thumbnail_size(604, 340); //16:9 ratio

	//additional image sizes
	add_image_size('jumbo_thumb', 604, 340,array('center','center')); //blog front page image

}

function btstarter_wp_setup() {

	add_theme_support( 'title-tag' );

	add_theme_support('custom-logo', array(
	    'height'      => 100,
	    'width'       => 400,
	    'flex-height' => true,
	    'flex-width'  => true,
	    'header-text' => array( 'site-title', 'site-description' ),
	));

	add_theme_support( 'automatic-feed-links' );
}
add_action( 'after_setup_theme', 'btstarter_wp_setup' );

//register functionality for custom menus
function bt_register_menu() {
	register_nav_menu('header-menu', __( 'Header Menu'));
}
add_action( 'init', 'bt_register_menu' );

function bt_widgets_init() {
    register_sidebar( array(
        'name'          => 'Footer - Copyright Text',
        'id'            => 'footer-copyright-text',
        'before_widget' => '<div class="footer_copyright_text">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ) );
     register_sidebar( array(
        'name'          => 'Sidebar',
        'id'            => 'sidebar-text',
        'before_widget' => '<div class="sidebar-item">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ) );
 
}
add_action( 'widgets_init', 'bt_widgets_init' );

// ======================================================================================
// enables featured blog posts


// ======================================================================================
// adds appropriate css classes to the map page css

/* REQUIRED FOR MAP TO BE FULL WIDTH */

add_filter( 'body_class', 'my_body_classes' );
function my_body_classes( $classes ) {

	if ( is_page_template( 'map.php')) {
		$classes[] = 'map-body';
	} else {
		$classes[] = 'bt-body';
	}
	return $classes;
}


//Eventually, here is where we add options for Appearance > Customize
// see here: https://www.lyrathemes.com/bootstrap-wordpress-theme-tutorial-1/
// - locate main logo - fix in cover.php

?>