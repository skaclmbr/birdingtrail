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
* x adjust infowindow behavior based on mobile vs. not (or screen size?) - done based on zoom level
* create buttons for zoom to state, zoom to location - overlay on gmap
* create buttons at top to toggle bfb, ebird observations?
* FORMATTING - MAKE NAV, MAP, FOOT FIT SCREEN EXACTLY, NO SCROLLING!

*/


/* =================================================================
* NCBT Map scripts loaded conditionally in functions.php
* get NCBT database connection information
*/

get_header(); ?>

<main role="main" class="inner map">
  <!-- The Modal -->
  <div class="modal fade" id="infoPanel">
    <div class="modal-dialog modal-dialog-centered"  >
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h3 class="modal-title" id="NAME"></h3>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- ADD SITE PHOTO FULL WIDTH OF MODAL -->
        
        <!-- Modal body -->
        <!-- ADD LOGO BUTTONS TO OPEN/CLOSE PANELS -->

				<div class="card">
  					<a href="#DESCRIPTION" class="btn btn-primary" data-toggle="collapse">Description</a>
  				</div>
				<div id="DESCRIPTION" class="collapse"></div>
				<div class="card">
  					<a href="#SPECIES" class="btn btn-primary" data-toggle="collapse">Species</a>
  				</div>
				<div id="SPECIES" class="collapse"></div>
				<div class="card">
  					<a href="#HABITATS" class="btn btn-primary" data-toggle="collapse">Habitats</a>
  				</div>
				<div id="HABITATS" class="collapse"></div>
				<div class="card">
  					<a href="#DIRECTIONS" class="btn btn-primary" data-toggle="collapse">Directions</a>
  				</div>
				<div id="DIRECTIONS" class="collapse"></div>

        
        <!-- Modal footer -->
        <div class="modal-footer">
          <div><a id="EXTWEBSITE" target="_blank">More information</a></div><br/>
          <div id="TRAVELINFO"></div>
          <div><a id="NAVIGATION" taget="_blank"></a></div>
        </div>
        </div>
        </div>
      </div>


      	<div id="map-container">
      		<!--bootstrap grid -->
      		<!-- this needs more work - figure out how to includ other buttons, mobile experience not great 
      		<div id="button-container"  class="container-fluid">
      			<span class="fa fa-stack" id="map-location">
			     <i class="fa fa-square fa-stack-2x background-fa "></i>
			     <i class="fa fa-location-arrow fa-stack-1x foreground-fa"></i>
			    </span>
      		</div>
      		-->
      		<!--google map canvas -->
	      	<div id="map_canvas"></div>
   		</div>
</main>
      
  


 <!-- Set Up Map -->
 
<script type="text/javascript">
	var currLatLng;
	var retDistDur;
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
	/* TEMPORARY DISABLE FOR TESTING */
	function tryGeolocation (logVisit) {
		if (typeof(logVisit)==='undefined') logVisit = true;
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
		  	currLatLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
		  	// set the new map center based on geolocation
		    map.setCenter({
		    	lat:position.coords.latitude,
		    	lng:position.coords.longitude
		    });
		    map.setZoom(10); //change this to modify based on screen size

		    if (logVisit) { //avoid logging visit if request from clicking map button
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
		  	};
		  });
		};
	};
	tryGeolocation();	

	jQuery(document).ready(function(){
		
		//listen for location button click
		jQuery("#map-location").click(function(){
			tryGeolocation(false);
		});

		//listen for modal panel close
		//make sure to clear out fields
		/*FOR NOW COMMENT OUT
		jQuery("#infoPanel").close(function(){
			clearInfoPanel(); //on maps.js
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

		// ===================================================================
		// add listener to resize google maps when browser resized


		// ===================================================================
		// map NCBT sites

		//populateNcbtPoints(); //populate points with static page-map.js data


		//setup marker definitions
		var siteMarkers = {};
		var siteInfoWindows = {};
		var siteMouseoverListeners = {};
		var siteMouseoutListeners = {};
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


		// ==================================================================
		// add listener to change display upon zoom change - functions in map.js
		var zoomDisplayLabels = 11;
		google.maps.event.addListener(map, 'zoom_changed', function() {
		    zoomLevel = map.getZoom();
		    console.log ("zoom level: " + String(zoomLevel));
		    if (zoomLevel >= zoomDisplayLabels) {
		    	showLabels(siteInfoWindows);
		    } else {
		    	removeLabels(siteInfoWindows);
		    	//loadMarkers(); //reload event listeners, map markers
		    	loadListeners(); //reload event listeners, map markers
		    }
		});

		function showLabels(sIW) {
		    //loop through passed infowindows array, TURN LABELS ON
		    for (l in sIW) {
		        siteInfoWindows[ l ].open(map,siteMarkers[ l ]);
		        google.maps.event.removeListener(siteMouseoutListeners [l]); //remove mouseout event listener (marker highlight, label disappear)
		        google.maps.event.removeListener(siteMouseoverListeners [l]); //remove mouseover event listener (marker highlight, label)
		    }
		}

		function removeLabels(sIW) {
		    //loop through passed infowindows array, TURN LABELS OFF
		    for (l in sIW) {
		    	siteInfoWindows[ l ].close();
		    	//siteMarkers[l].setMap(null); //remove old markers
		    }
		}

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
			
			//could wrap mouseover, mouseout event listeners into a function to call later?

			/*create two functions, one for markers, one for labels and event listeners*/
			$markers = 'function loadMarkers() {';
			$listeners = 'function loadListeners() {';

			$sql = "SELECT * FROM site_data";
			$result = $conn->query($sql);
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$slug = $row["SITESLUG"];


					$listeners .='iwContent = "<div class=\"site-popup\">' . $row['NAME'] . '</div>";';
					$listeners .= 'siteInfoWindows["' . $slug . '"] = new InfoBox(infoboxOptions);';
					$listeners .= 'siteInfoWindows["' . $slug . '"].setContent(iwContent);';

					$markers .= 'siteMarkers["' . $slug . '"] = new google.maps.Marker({position:{lat:' . $row["LAT"] . ', lng:' . $row["LON"] . '},icon:siteIcon,map:map});';
					$listeners .= 'siteMouseoverListeners["' . $slug . '"] = google.maps.event.addListener(siteMarkers["' . $slug . '"], "mouseover", function() {siteMarkers["' . $slug . '"].setIcon(highlightIcon); siteInfoWindows["' . $slug . '"].open(map,siteMarkers["' . $slug . '"]); });';
					$listeners .= 'siteMouseoutListeners["' . $slug . '"] = google.maps.event.addListener(siteMarkers["' . $slug . '"], "mouseout", function() {siteMarkers["' . $slug . '"].setIcon(siteIcon); siteInfoWindows["' . $slug . '"].close();});';

					$markers .= 'google.maps.event.addListener(siteMarkers["' . $slug . '"], "click", function() {
						triggerInfoPanel("'. $slug . '");
						});';
					/* WORKING CODE COMMENTED OUT FOR TESTING
					echo 'iwContent = "<div class=\"site-popup\">' . $row['NAME'] . '</div>";';
					echo 'siteInfoWindows["' . $slug . '"] = new InfoBox(infoboxOptions);';
					echo 'siteInfoWindows["' . $slug . '"].setContent(iwContent);';

					echo 'siteMarkers["' . $slug . '"] = new google.maps.Marker({position:{lat:' . $row["LAT"] . ', lng:' . $row["LON"] . '},icon:siteIcon,map:map});';
					echo 'siteMouseoverListeners["' . $slug . '"] = google.maps.event.addListener(siteMarkers["' . $slug . '"], "mouseover", function() {siteMarkers["' . $slug . '"].setIcon(highlightIcon); siteInfoWindows["' . $slug . '"].open(map,siteMarkers["' . $slug . '"]); });';
					echo 'siteMouseoutListeners["' . $slug . '"] = google.maps.event.addListener(siteMarkers["' . $slug . '"], "mouseout", function() {siteMarkers["' . $slug . '"].setIcon(siteIcon); siteInfoWindows["' . $slug . '"].close();});';

					echo 'google.maps.event.addListener(siteMarkers["' . $slug . '"], "click", function() {
						triggerInfoPanel("'. $slug . '");
						});';
					*/
				}
				$markers .= 'console.log("loadMarkers run");};'; //close out function
				echo $markers;

				$listeners .= 'console.log("loadListeners run");};'; //close out function
				echo $listeners;
			}
			
		?>
		loadMarkers();
		loadListeners();
	});
</script>
<?php get_footer(); ?>