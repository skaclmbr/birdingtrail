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
	wp_enqueue_script( 'custom-script' );

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
				wp_enqueue_script('ncbt-google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyC1D62u_pN2PRUT6gCBkfZoZXiVU1F4Vxk&v=3'); //old key
				wp_enqueue_script('infobox', get_stylesheet_directory_uri() . '/js/infobox.js', array('jquery'));
				wp_enqueue_script('ncbt_map', get_stylesheet_directory_uri() . '/js/map.js');
				//wp_enqueue_style('ncbt-map-style', get_stylesheet_directory_uri() . '/map.css');

				break;
		}
	}
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
//add_action( 'wp_ajax_my_action', 'my_action' );

function get_ncbt_data() {

	if(!isset($_POST['dbrequest'])) {
		echo json_encode(array('ncbt_data_success'=>'response_missing'));
		wp_die();

	} else {

		$request = strval( $_POST['dbrequest'] );
		//$response_safe = filter_var($siteslug, FILTER_SANITIZE_STRING); //OLD VERSION - reinstate?
	    switch ($request) {
	    	case "site_detail":
	    		$siteslug = strval( $_POST['siteslug']);
				//Connect to database, get site data
				$servername = "localhost";
				$username = "ncbirdin_ncbtweb";
				$password = "9%VI&p&Yo844";

				//connect to NCBT Site Data
				$dbname = "ncbirdin_ncbt_data";
				$conn = new mysqli($servername, $username, $password, $dbname);
				
				$sql = 'SELECT * FROM site_data WHERE SITESLUG = "' . $siteslug . '" LIMIT 1'; //will only return one record
				//$sql = "SELECT * FROM site_data"; //get all data
				//$result = $conn->query($sql);
				$result = $conn->query($sql);

				echo json_encode($result->fetch_assoc()); //return results - single record
							
				//loop through resulting records
				/*		
				$rows = array();
				if($result->num_rows > 0) {
					while($r = $result->fetch_assoc()) {
						$rows[] = array('SITESLUG'=>$r['SITESLUG'], 'TITLE'=>$r['TITLE'],'LAT'=>$r['LAT'],'LON'=>$r['LON']);
						$slug = $row["SITESLUG"];
						array_push(json_encode(array('ncbt_data_success'=>$slug));

					};
				};
				echo json_encode($rows); //return results
				*/
				wp_die(); //close DB connection
	    		break; //end switch code
    		case "log_visit": //post website visit data
				$servername = "localhost";
				$username = "ncbirdin_ncbtweb";
				$password = "9%VI&p&Yo844";

				//connect to NCBT Site Data
				$dbname = "ncbirdin_ncbt_data";
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
    		default:
    			echo "no data";
    			break;

		
		}

	}
}
// ======================================================================================
// adds appropriate css classes to the map page css

/* REQUIRED FOR MAP TO BE FULL WIDTH */

add_filter( 'body_class', 'my_body_classes' );
function my_body_classes( $classes ) {

	if ( is_page_template( 'map.php')) {
		$classes[] = 'map-body';
	}
	return $classes;
}


//Eventually, here is where we add options for Appearance > Customize
// see here: https://www.lyrathemes.com/bootstrap-wordpress-theme-tutorial-1/
// - locate main logo - fix in cover.php

?>