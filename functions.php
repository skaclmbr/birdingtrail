<?php 

function wpbootstrap_scripts_with_jquery()
{
	global $post;
	//wp_enqueue_script('jquery', get_stylesheet_directory_uri() . '/js/jquery-3.3.1.slim.min.js'); //not sure if needed?
	wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/bootstrap/css/bootstrap.css');
	wp_enqueue_style('parent-style', get_stylesheet_directory_uri() . '/style.css',array('bootstrap'));
	wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
	//wp_enqueue_style('bootstrap-resp', get_stylesheet_directory_uri() . '/bootstrap/css/bootstrap-responsive.css');

	// Register the script like this for a theme:
	wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', array( 'jquery' ), '4.0.0', false );
	//wp_enqueue_script('popover', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js', array('bootstrap','bootstrap-js'));
	// wp_enqueue_script( 'custom-script' );

	//google fonts
	wp_register_style('google_fonts','https://fonts.googleapis.com/css?family=Pacifico',array(),null);
	wp_enqueue_style('google_fonts');

	if (is_page() || is_single())
	{
		switch($post->post_name) //post_name is the post slug which is more consistent
		{
			case 'sites':
				//scripts for running the google maps
				//wp_enqueue_script('get_ncbt_data', get_stylesheet_directory_uri() . '/js/ncbt_data.js'); //not needed, incorporated into page-map.js
				//wp_enqueue_script('ncbt-google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAn50b-GXINXayDZRR1k76y5ZhDLc2mn5o&v=3'); //new key
				//wp_enqueue_script('ncbt-google-map', 'https://maps.googleapis.com/maps/api/js'); //new key
				//wp_enqueue_script('ncbt-google-jsapi','https://www.google.com/jsapi');
				//wp_enqueue_script('static_ncbt_points', get_stylesheet_directory_uri() . '/js/20180227_ncbt_points_static.js');
				//get_template_part('page-templates/ncbt','dbconnect');
				//wp_enqueue_script('jquery-mobile', get_stylesheet_directory_uri() . '/js/jquery.mobile-1.4.5.min.js', array(), null, true); //enables infopanel appear/disappear - note can custom create files needed for theme desired on website - would reduce download time
				wp_enqueue_script('ncbt-google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyC1D62u_pN2PRUT6gCBkfZoZXiVU1F4Vxk&v=3&libraries=places'); //old key, enable place id retrieval
				// wp_enqueue_script('infobox', get_stylesheet_directory_uri() . '/js/infobox.js', array('jquery')); //not needed
				wp_enqueue_script('ncbt_map', get_stylesheet_directory_uri() . '/js/map.js');
				//wp_enqueue_style('ncbt-map-style', get_stylesheet_directory_uri() . '/map.css');

				break;
			case 'blog': //this doesn't seem to be triggered - CONSIDER REMOVING
				wp_enqueue_style('blog-css', get_stylesheet_directory_uri() . '/css/blog.css');
				break;
		}
	}
}
add_action( 'wp_enqueue_scripts', 'wpbootstrap_scripts_with_jquery' );



//==============================================================================
//These functions feed NCBT or BFB data to a map page


//Adds ajaxurl variable to JS on pages, used in ajax calls
add_action('wp_head','pluginname_ajaxurl');
function pluginname_ajaxurl() {
	?>
	<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script>
	<?php
}

//Retrieves site data from database
add_action( 'wp_ajax_get_ncbt_data', 'get_ncbt_data' );
add_action( 'wp_ajax_nopriv_get_ncbt_data', 'get_ncbt_data' );

function get_ncbt_data() {

	if(!isset($_POST['dbrequest'])) {
		echo json_encode(array('ncbt_data_success'=>'response_missing'));
		wp_die();

	} else {

		$request = strval( $_POST['dbrequest'] );

		//$response_safe = filter_var($siteslug, FILTER_SANITIZE_STRING); //OLD VERSION - reinstate?

		// get db login information
		include('db_info.php');

	    switch ($request) {
	    	case "site_markers":
	    		// build and return javascript functions to place markers and labels on the map for sites

				//======================================================================
				//The following is working code that downloads the most recent ncbt data
				//TODO: Rewrite this function to be included in the functions.php get_ncbt_data() function (remove duplication)
				//POTENTIAL FUTURE - move this to a CRON JOB that runs each night and creates static js file (if it speeds loading)
				//======================================================================

				//Connect to database, get site data
				// get db login information
				include('db_info.php');

				$conn = new mysqli($servername, $username, $password, $dbname);
				// Check connection
				if ($conn->connect_error) {
				   die("console.log('Connection failed');");
				}; 
				// else {
				//   echo "console.log('Connected successfully - creating markers and listeners');\n";
				// };

				//RETRIEVE ONLY DATA NEEDED TO PLACE MARKERS ON MAP
				$sql = "SELECT SITESLUG, TITLE, LAT, LON FROM site_data";
				$result = $conn->query($sql);


				//loop through results, build JSON object
				$rows = array();
				if ($result->num_rows >0 ){
					while($r = $result->fetch_assoc()) {
						$rows[] = $r;
					}
				}

				echo json_encode($rows); //return results - NO RECORD RESTRICTION!
							
				wp_die(); //close DB connection
	    		break;

	    	case "test":
	    		//troubleshooting issue...
	    		echo "successful test!";

				wp_die(); //close DB connection
	    		break;

	    	case "site_detail":
	    	//TESTING
	    		// build and return javascript functions to place markers and labels on the map for sites

				//======================================================================
				//The following is working code that downloads the most recent ncbt data
				//TODO: Rewrite this function to be included in the functions.php get_ncbt_data() function (remove duplication)
				//POTENTIAL FUTURE - move this to a CRON JOB that runs each night and creates static js file (if it speeds loading)
				//======================================================================

				//Connect to database, get site data
				// get db login information
				include('db_info.php');

				$siteslug = strval( $_POST['slug']); //for some reason, produces error when tag is 'siteslug' - WTF?

				$conn = new mysqli($servername, $username, $password, $dbname);
				// Check connection
				if ($conn->connect_error) {
				   die("console.log('Connection failed');");
				}; 
				// else {
				//   echo "console.log('Connected successfully - creating markers and listeners');\n";
				// };

				//RETRIEVE ONLY DATA NEEDED TO PLACE MARKERS ON MAP
				// $sql = "SELECT SITESLUG, TITLE, LAT, LON FROM site_data";
				$sql = 'SELECT * FROM site_data WHERE SITESLUG = "' . $siteslug . '" LIMIT 1'; //will only return one record
				// $sql = 'SELECT * FROM site_data WHERE SITESLUG = "anderson-point-park" LIMIT 1'; //will only return one record
				$result = $conn->query($sql);


				//loop through results, build JSON object
/*				$rows = array();
				if ($result->num_rows >0 ){
					while($r = $result->fetch_assoc()) {
						$rows[] = $r;
					}
				}
*/
				// echo $siteslug;
				echo json_encode($result->fetch_assoc()); //return results - NO RECORD RESTRICTION!
							
				wp_die(); //close DB connection
	    		break;


    		case "log_visit": //post website visit data
				// $servername = "localhost";
				// $username = "ncbirdin_ncbtweb";
				// $password = "9%VI&p&Yo844";

				// //connect to NCBT Site Data
				// $dbname = "ncbirdin_ncbt_data";
				$conn = new mysqli($servername, $username, $password, $dbname);
				
				//data passed from successful geolocation
	    		$platform = strval( $_POST['platform']);
	    		$browser = strval( $_POST['browser']);
	    		$userid = strval( $_POST['ncbtuserid']);
	    		$lat = doubleval( $_POST['lat']);
	    		$lon = doubleval( $_POST['lon']);


				//$sql = "INSERT INTO visits (PLATFORM, LAT, LON) VALUES ('test',35,85)"; //TESTING
				$sql = "INSERT INTO visits (PLATFORM, BROWSER, NCBTUSERID, LAT, LON) VALUES ('" . $platform . "', '" . $browser . "','" . $userid . "'," . $lat . "," . $lon . ")"; //post data

				if ($conn->query($sql) === TRUE) {
					echo "New record created successfully";
				} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
				}
				$conn->close();
				wp_die(); //close db connection
				break; //end switch code evaluation

    		case "update_site_info": //update one field of site information
				// $servername = "localhost";
				// $username = "ncbirdin_ncbtweb";
				// $password = "9%VI&p&Yo844";

				// //connect to NCBT Site Data
				// $dbname = "ncbirdin_ncbt_data";
				$conn = new mysqli($servername, $username, $password, $dbname);
				
				//data passed from successful geolocation
	    		$field = strval( $_POST['field']);
	    		$data = strval( $_POST['data']);
	    		$siteslug = strval( $_POST['slug']);


				//$sql = "INSERT INTO visits (PLATFORM, LAT, LON) VALUES ('test',35,85)"; //TESTING
				$sql = "UPDATE site_data SET " . $field . " = '" . $data . "' WHERE SITESLUG = '" . $siteslug . "'"; //post data

				if ($conn->query($sql) === TRUE) {
					echo "Record updated successfully";
				} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
				}
				$conn->close();
				wp_die(); //close db connection
				break; //end switch code evaluation

    		default:
    			echo "no data";
    			break;

		
		}

	}
}
// ======================================================================================
// add customization for theme

//enables the featured image functionality on blog posts

if (function_exists( 'add_theme_support')) {
	add_theme_support('post-thumbnails');

	set_post_thumbnail_size(120,9999,true);

	//additional image sizes
	//add_image_size('jumbo-thumb', 300, 9999, true); //blog front page image
	add_image_size('jumbo-thumb', 480, 340, true); //blog front page image

}


/*
if (function_exists( 'add_theme_support' )) {
	set_post_thumbnail_size(150,150, true); //default featured image dimensions

	//additiaonl image sizes
	//add_image_size( 'category_thumb', 300, 9999); //300px wide and unlimited height
}
*/

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