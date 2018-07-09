<?php
/**
 * Template Name: Map
 *
 * The template for the page builder full-width.
 *
 * It contains header, footer and 100% content width.
 *
 * @package birdingtrail
 * @since birdingtrail
 * @author Scott Anderson
 *
 * ADD ENQUEUE SCRIPTS HERE (GMAPS, NCBT ADD DATA)
 */

/* =================================================================
* THINGS TO DO
* adjust zoom level to display size
* adjust infowindow behavior based on mobile vs. not (or screen size?)
* on mobile, make top bar disappear, then reappear on scroll

*/


/* =================================================================
* NCBT Map scripts loaded conditionally in functions.php
* get NCBT database connection information
*/

get_header(); ?>
<!-- FIGURE OUT HOW TO BUILD AND HOST LOCAL VERSIONS OF THESE 
<link rel="stylesheet" href="http://test.ncbirdingtrail.org/wp-content/themes/minimumminimal-child/js/jquery.mobile-1.4.5.min.css" />
<script src="http://test.ncbirdingtrail.org/wp-content/themes/minimumminimal-childjquery.mobile-1.4.5.min.js"></script>
-->
<!--<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>-->



<!--<div id="map_wrapper" role="main" class="inner cover">-->
	<!--<div id="map_header" data-role="header"></div>-->
<main role="main" class="container-fluid">
<!--<div id="map_canvas"></div>-->
		<!--<a href="#info_panel" class="ui-btn-icon-notext" data-role="button" data-inline="true" data-icon="bars">Test</a>-->
	<!--<div id="map_footer" data-role="footer"></div>-->
</main>

<!-- NCBT SITE DETAIL PANEL -->

<!-- DEBUGGING - DISABLE FOR NOW
	

	<div data-role="panel" id="info_panel" class="ui-overlay-shadow" data-position="left" data-display="overlay" data-theme="a">
		<ul data-role="listview" data-theme="a">
			<li data-icon="delete"><a href="#" data-rel="close">Close</a></li>
			<li data-role="list-divider" id="title"><h1 id="NAME"></h1><br/><a id="EXTWEBSITE" target="_blank">External Website</a></li>
		</ul>

		<div data-role="collapsible" data-inset="false" data-iconpos="right" data-theme="a" data-content-theme="a">
			<h2>Description</h2>
			<p id="DESCRIPTION"></p>
		</div>
		<div data-role="collapsible" data-inset="false" data-iconpos="right" data-theme="a" data-content-theme="a">
			<h2>Species of Interest</h2>
			<p id = "SPECIES"></p>
		</div>
		<div data-role="collapsible" data-inset="false" data-iconpos="right" data-theme="a" data-content-theme="a">
			<h2>Habitat</h2>
			<p id="HABITATS"></p>
		</div>
		<div data-role="collapsible" data-inset="false" data-iconpos="right" data-theme="a" data-content-theme="c">
			<h2>Directions</h2>
			<p id="DIRECTIONS">Look it up on Google.</p>
		</div>
	</div>
-->
	<!-- ARCHIVED INFO PANEL HTML
	<div id="info_panel" class="ui-overlay-shadow" data-role="panel" data-position="left" data-display="overlay" data-theme="b">
		<div id="info_panel_content">
			<div id="closediv" ><a href="#" data-rel="close" data-icon="delete" data-theme="a" data-icon="delete" data-inline="true">Close</a></div>
			<div id="info_panel_site_data">
				<div id="title"><h1 id="NAME"></h1></div>
				<div class="region_group">
					<a id="EXTWEBSITE" target="_blank">External Website</a>
					</div>
				<div class="item">
					<div class="site_description_text" id="description_div">
						<h2>Description</h2>
						<p id="DESCRIPTION"></p>
					</div>
					<div class="map_window" id="map_window_div"></div>
				</div>
				<div class="item" id="species_div">
					<h2>Species of Interest</h2>
					<p id = "SPECIES"></p>
				</div>
				<div class="item" id="habitats_div">
					<h2>Habitat</h2>
					<p id="HABITATS"></p>
				</div>
				<div class="item_directions">
					<div class="item directions_text" id="directions_div">
						<h2>Directions</h2>
						<p id="DIRECTIONS">Look it up on Google.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
-->
<!--</div> map wrapper-->
<!-- Set Up Map -->
<script type="text/javascript">

	// Cookies
	// add cookie to see if user has allowed geolocation previously

	function checkCookie(cookiename) {
	    var user = getCookie(cookiename);
	    return user;
	}

	function setCookie (cname, cvalue) {
		var exdays = 730; //this can also be passed as a variable
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires=" + d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var decodedCookie = decodeURIComponent(document.cookie);
	    var ca = decodedCookie.split(';');
	    for(var i = 0; i <ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0) == ' ') {
	            c = c.substring(1);
	        }
	        if (c.indexOf(name) == 0) {
	            return c.substring(name.length, c.length);
	        }
	    }
	    return "";
	}

	function guid() {
    	return (((1+Math.random())*0x10000)|0).toString(16).substring(1) + "-" + (((1+Math.random())*0x10000)|0).toString(16).substring(1) + "-" + (((1+Math.random())*0x10000)|0).toString(16).substring(1); 
	}

	// Try HTML5 geolocation.
	/* TEMPORARY DISABLE FOR TESTING
	if (navigator.geolocation) {
	  navigator.geolocation.getCurrentPosition(function(position) {
	  	console.log("geolocation allowed");
	  	
	  	//check for cookie (repeat user)
	  	var ncbtUserId = guid();
	  	if (getCookie('ncbtUserId')) {
	  		ncbtUserId = getCookie('ncbtUserId');	
	  	} else {
			setCookie('ncbbtUserId',ncbtUserId);
	  	};

	  	// set the new map center based on geolocation
	    map.setCenter({
	    	lat:position.coords.latitude,
	    	lng:position.coords.longitude
	    });
	    //map.setZoom(10); //change this to modify based on screen size

	    //Post location data to Visit Table
	    //COULD ADD CODE TO PARSE navigator.userAgent and deterimine if mobile or not, other factors jermain to location
	    data = jQuery.ajax({
	        type: "POST",
	        url: ajaxurl, //url for WP ajax php file, var def added to header in functions.php
	        data: {
	            'action': 'get_ncbt_data', //server side function
	            'dbrequest': 'log_visit', //add to the visit table
	            'platform': navigator.platform, //get OS information
	            'browser': navigator.userAgent, //the name of the browser
	            'ncbtuserid': ncbtUserId, //id of the user from the cookie
	            'lat' : position.coords.latitude, //latitude
	            'lon' : position.coords.longitude //longitude
	        },
			success: function(data) {
			  console.log(data);
	        }
	    });
	  
	  });
	};
	*/

	jQuery(document).ready(function(){
		

		//when scrolling hide/show header
		/*
		jQuery(window).scroll(function(){
		    control.log("scrolling!");
		});
		*/

		//jQuery("info_panel").hide();
		var defaultLatLng = new google.maps.LatLng(35.2,-79.8); // offcenter to allow for panel
		var defaultZoomLevel = 8;

		//var ncbtMapStyle = [{ featureType: "administrative", stylers: [ { visibility: "on" }, { saturation: -90 }, { lightness: 52 } ] }];
		
		var myOptions = {
	          zoom: defaultZoomLevel,
	          panControl: false,
	          streetViewControl: false,
			  mapTypeId: google.maps.MapTypeId.TERRAIN,
			  //mapTypeControlOptions: {position: google.maps.ControlPosition.TOP_LEFT},
			  center: defaultLatLng,
			  styles: ncbtMapStyle
			};

		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);


		console.log("map run completed");


		// ===================================================================
		// map NCBT sites

		//populateNcbtPoints(); //populate points with static page-map.js data


		//setup marker definitions
		var siteMarkers = {};
		var siteInfoWindows = {};
		var pointOffset = new google.maps.Point(13,13)
		siteIcon = {
			path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
			fillColor: '#365c8b',
			fillOpacity: .8,
			strokeWeight: 0,
			scale:0.25,
			anchor: pointOffset
			};
		highlightIcon = {
			path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
			fillColor: '#f6e927',
			fillOpacity: .8,
			strokeWeight: 2,
			strokeColor: '#365c8b',
			scale:0.25,
			anchor: pointOffset
			};

		var infoboxOptions = {
			boxStyle: {
			  //opacity:0.9
			  textAlign: "center"
			  ,fontSize: "8pt"
			  ,maxWidth: "150px"
			 }
			,disableAutoPan: true
			,pixelOffset: new google.maps.Size(15,-25)
			,closeBoxURL: ""
			,isHidden: false
			,pane: "mapPane"
			,infoBoxClearance: new google.maps.Size(1,1)
			,pane: "floatPane"
			,enableEventPropagation: true
			};

		<?php
			
			//======================================================================
			//The following is working code that downloads the most recent ncbt data
			//Currently loading from static js file 20180227_ncbt_points_static.js
			//FUTURE - move this to a CRON JOB that runs each night and creates static file (if it speeds loading)
			//======================================================================

			//include_once('ncbt_connect.php'); //attempt to put login info in another file - not sure this is necessary

			//Connect to database, get site data
		    $servername = "localhost";
		    $username = "ncbirdin_ncbtweb";
		    $password = "9%VI&p&Yo844";
		    $dbname = "ncbirdin_ncbt_data";

			$conn = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($conn->connect_error) {
			   die("console.log('Connection failed');");
			} else {
			  echo "console.log('Connected successfully');";
			}
			


			$sql = "SELECT * FROM site_data";
			$result = $conn->query($sql);

			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$slug = $row["SITESLUG"];
					echo 'iwContent = "<div class=\"site-popup\">' . $row['NAME'] . '</div>";';
					//<hr/><div style=\"font-weight:normal;font-variant:normal;font-size:1.1em;\">"+ region + " : " + group + "</div></div>";';
					echo 'siteInfoWindows["' . $slug . '"] = new InfoBox(infoboxOptions);';
					echo 'siteInfoWindows["' . $slug . '"].setContent(iwContent);';

					echo 'siteMarkers["' . $slug . '"] = new google.maps.Marker({position:{lat:' . $row["LAT"] . ', lng:' . $row["LON"] . '},icon:siteIcon,map:map});';
					echo 'google.maps.event.addListener(siteMarkers["' . $slug . '"], "mouseover", function() {siteMarkers["' . $slug . '"].setIcon(highlightIcon); siteInfoWindows["' . $slug . '"].open(map,siteMarkers["' . $slug . '"]); });';
					echo 'google.maps.event.addListener(siteMarkers["' . $slug . '"], "mouseout", function() {siteMarkers["' . $slug . '"].setIcon(siteIcon); siteInfoWindows["' . $slug . '"].close();});';
					//echo 'google.maps.event.addListener(siteMarkers["' . $slug . '"], "click", function() {jQuery( "info_panel" ).show( "slow" );});';

					echo 'google.maps.event.addListener(siteMarkers["' . $slug . '"], "click", function() {
						triggerInfoPanel("'. $slug . '");
						});';

				}
			}
			
		?>

	});
</script>


<?php get_footer(); ?>
