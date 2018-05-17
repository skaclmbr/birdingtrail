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

<!-- 
	content for popup box and map container
	resizes google map container based on viewport size and header, footer heights
		- see code in initialize below,  

-->
<main id="map-modal-container" role="main" class="inner map">
  <!-- The Modal -->
  <div class="modal fade" id="infoPanel">
    <div class="modal-dialog modal-dialog-centered"  >
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="container site-modal-header">
	       <img id="modal-header-image"/>
    	  <div class="modal-site-title row">
	          <div class="col-10"><h3 class="modal-title" id="NAME"></h3></div>
	          <div class="col-2"><button type="button" id="close-modal" class="close" data-dismiss="modal">&times;</button></div>
          </div>
          <div class="row">
          	<div class="col-12"><span id="site-open-status" class="badge"></span></div>
          </div>
        </div>
        <!-- ADD SITE PHOTO FULL WIDTH OF MODAL -->
        
        <!-- Modal body -->
        <!-- ADD LOGO BUTTONS TO OPEN/CLOSE PANELS -->

				<div class="card">
  					<a href="#DESCRIPTION" class="btn btn-primary" data-toggle="collapse">Description</a>
  				</div>
				<div id="DESCRIPTION" class="collapse site-info"></div>
				<div class="card">
  					<a href="#SPECIES" class="btn btn-primary" data-toggle="collapse">Species</a>
  				</div>
				<div id="SPECIES" class="collapse site-info"></div>
				<div class="card">
  					<a href="#HABITATS" class="btn btn-primary" data-toggle="collapse">Habitats</a>
  				</div>
				<div id="HABITATS" class="collapse site-info"></div>
				<div class="card">
  					<a href="#DETAILS" class="btn btn-primary" data-toggle="collapse">Details</a>
				</div>
				<div id="DETAILS" class="collapse  site-info">
					<div id="HOURS">
					</div>
				</div>
        
        <!-- Modal footer -->
        <div class="row modal-footer">
          <div id="TRAVELINFO" class="col-md"></div>
          <div class="col-md"><a id="EXTWEBSITE" target="_blank"><i class="fa fa-info-circle"></i></a></div><br/>
          <div class="col-md"><a id="NAVIGATION" taget="_blank"></a></div>
		  <!--<div> <a id="GOOGLELINK" href="" target="_blank"><span class="badge badge-secondary">Google Maps</span></a></div>-->
		  <!--<div> <a id="GOOGLELINK" href="" target="_blank"><img src="<?php echo get_template_directory_uri() . '/img/google_maps.png' ?>"/></a></div>-->
		  <div> <a id="GOOGLELINK" href="" target="_blank"><i class="fa fa-google"></a></div>
					<!-- be sure to include link to Google Maps Page here -->
		  <div><a id="twitter-share" href="" target="_blank"><i class="fa fa-twitter-square"></i></a></div>
        </div>
      </div>
    </div>
   </div>


	<div id="map-container" class="row">
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
		<!-- zoom controls, add search? 
		<div id="map_tools"><i id="center-map" class="fa fa-crosshairs"></i><i id="zoom-state" class="fa fa-search-minus"></i></div>
  		-->
  		<div id="map_canvas" class="col-md-12"></div>
	</div>
</main>
      
  


 <!-- Set Up Map -->
 
<script type="text/javascript">
	var currLatLng;
	var retDistDur;

	jQuery('#page-container').attr("padding","0"); //for map page, no margins or padding
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
		  	//console.log("geolocation allowed");
		  	
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
					  //console.log(data);
			        }
			    });
		  	};
		  });
		};
	};

	// ===================================================================
	// add listener to resize google maps when browser resized

	//set map height function
	function setMapHeight() {
		setTimeout(function(){
			 //determine header, footer heights
			 hHeight = parseInt(jQuery(".masthead").css("height"));
			 fHeight = parseInt(jQuery(".mastfoot").css("height"));
			 fhHeight = fHeight+hHeight;
			 bHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0); //maximum dimensions for window

			 hoHeight = parseInt(jQuery(".masthead").outerHeight(true));
			 foHeight = parseInt(jQuery(".mastfoot").outerHeight(true));
			 fhoHeight = hoHeight + foHeight;
			 
			 mHeight = bHeight - fhoHeight - 5;

			/*
			 console.log("hh: " + String(hHeight));
			 console.log("fh: " + String(fHeight));
			 console.log("fhh: " + String(fhHeight));
			 console.log("bh: " + String(bHeight));
			 console.log("mh: " + String(bHeight - fhHeight));

			 console.log("hoh: " + String(hoHeight));
			 console.log("foh: " + String(foHeight));
			 console.log("fohh: " + String(fhoHeight));
			 console.log("mohh: " + String(mHeight));
			*/

			 jQuery("#map_canvas").css("height",mHeight); //set map canvas to new dimensions (window - footer + header);
			 jQuery("#map-modal-container").css("height",mHeight); //set map canvas to new dimensions (window - footer + header);

		}, 0);
	};
	// ===================================================================
	// add custom buttons to the map
	// allows zoom out to state, or zoom to location

	// Create a div to hold the control.
	function ButtonControl(controlDiv, map) {
		// Set CSS for the control border
		var controlUI = document.createElement('div');
		controlUI.className = "btn-group";
		controlUI.id = "map-button-group";
		controlUI.setAttribute("role","group");
		controlDiv.appendChild(controlUI);

		// Set CSS for the control interior
		var centerButton = document.createElement('button');
		centerButton.className = 'btn btn-light';
		/*
		centerButton.setAttribute('data-toggle','tooltip');
		centerButton.setAttribute('title','Zoom to Location');
		*/
		centerButton.innerHTML = "<i class='fa fa-crosshairs'></i>";
		controlUI.appendChild(centerButton);

		var stateButton = document.createElement('button');
		stateButton.className = 'btn btn-light';
		stateButton.innerHTML = "<i class='fa fa-search-minus'></i>";
		/*
		stateButton.setAttribute('data-toggle','tooltip');
		stateButton.setAttribute('title','Zoom to North Carolina');
		*/
		controlUI.appendChild(stateButton);

		 // Setup the click event listeners: simply set the map to Chicago.
        centerButton.addEventListener('click', function() {
          console.log("center button click");
          tryGeolocation();
        });
        stateButton.addEventListener('click', function() {
          console.log("state button click");
          zoomState(map);
        });
    }

    function zoomState(map){
    	//set parameters to zoom map to include the entire state, given the map dimensions
    	mObj = jQuery('#map_canvas');
    	var mWidth = parseInt(mObj.outerWidth(true));
		var centerLatLng = new google.maps.LatLng(35.2,-79.8); // offcenter to allow for panel
		var zoomLevel = 8;

    	if (mWidth<800) {
    		zoomLevel = 6
    	} else if (mWidth>1700) {
    		zoomLevel = 8
    	} else {
    		zoomLevel = 7
    	}

    	map.setCenter(centerLatLng);
    	map.setZoom(zoomLevel);

    	// 667px - zoom 6
    	// 375px - zoom 6
    	// 480px - zoom 6
    	// 800px - zoom 7 minimum/break point
    	// 1700px - zoom 8 minimum/break point

    }

	jQuery(document).ready(function(){
	//jQuery(function(){
		
		//attempt to get geolocation from browser window
		tryGeolocation();	

		
		// ================================================================
		// load listeners
		//listen for location button click
		jQuery("#map-location").click(function(){tryGeolocation(false);});

		//add listener to change map size when window changes
		google.maps.event.addDomListener(window, "resize", function() {setMapHeight();});

		//add listener to recenter map on location
		jQuery('#center-map').click(tryGeolocation());

		//listen for modal panel close
		//make sure to clear out fields
		/*FOR NOW COMMENT OUT*/
		//google.maps.event.addDomListener(jQuery("#infoPanel"), "close")
		
		jQuery("#close-modal").click(function(){
			console.log("modal panel closed");
			clearModalPanel(); //on maps.js
		});
		

		var defaultLatLng = new google.maps.LatLng(35.2,-79.8); // offcenter to allow for panel
		var defaultZoomLevel = 8;

		var myOptions = {
	          zoom: defaultZoomLevel,
	          panControl: false,
	          streetViewControl: false,
			  mapTypeId: google.maps.MapTypeId.TERRAIN,
			  center: defaultLatLng,
			  fullscreenControl:false, //disable this so that we can put a new button group in upper right
			  mapTypeControl:false, //disable the regular, terrain, and satellite options to make room for BFB buttons
			  styles: ncbtMapStyle
			};

		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		//console.log('map initiated');




        // Create the DIV to hold the center and zoom to state buttons
        // constructor passing in this DIV.
        var buttonControlDiv = document.createElement('div');
        var buttonControl = new ButtonControl(buttonControlDiv, map);

        buttonControlDiv.index=1;
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(buttonControlDiv);

        // Create the DIV to hold the NCBT and BFB buttons
        // constructor passing in this DIV.
        /*
        var buttonControlDiv = document.createElement('div');
        var buttonControl = new ButtonControl(buttonControlDiv, map);

        buttonControlDiv.index=1;
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(buttonControlDiv);
        */


 		// run function to set map height on page load
		setMapHeight();



		// ===================================================================
		// map Birding Trail sites

		//populateNcbtPoints(); //populate points with static page-map.js data

		//setup marker definitions
		var siteMarkers = {};
		var siteInfoWindows = {};
		var siteMouseoverListeners = {};
		var siteMouseoutListeners = {};
		var siteLabelListeners = {};
		var pointOffset = new google.maps.Point(13,13)
		siteIcon = {
			path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
			fillColor: '#365c8b',
			fillOpacity: .8,
			strokeWeight: 0,
			scale:0.25
			};
		highlightIcon = {
			path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
			fillColor: '#f6e927',
			fillOpacity: .8,
			strokeWeight: 2,
			strokeColor: '#365c8b',
			scale:0.25
			};

		bfbIcon = {
			path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
			fillColor: '#4e2904',
			fillOpacity: .8,
			strokeWeight: 0,
			scale:0.25
			};
		bfbhighlightIcon = {
			path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
			fillColor: '#f6e927',
			fillOpacity: .8,
			strokeWeight: 2,
			strokeColor: '#4e2904',
			scale:0.25
			};

		//These options govern the look of the labels that appear on the map
		var infoboxOptions = {
			boxStyle: {
			  opacity:0.9
			  ,textAlign: "center"
			  ,fontSize: "8pt"
			  ,maxWidth: "150px"
			 }
			,disableAutoPan: true
			,pixelOffset: new google.maps.Size(12,-25)
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
		var currZoom = map.getZoom();
		google.maps.event.addListener(map, 'zoom_changed', function() {
		    newZoom = map.getZoom();
		    console.log ("zoom level: " + String(newZoom));
		    if (newZoom >= zoomDisplayLabels && currZoom<zoomDisplayLabels) { //zoomed in, crossed label zoom threshold
		    	showLabels(siteInfoWindows); //turn all labels on, remove hover events
		    } else if (newZoom < zoomDisplayLabels && currZoom >=zoomDisplayLabels ) { //zoomed out, crossed label zoom threshold
		    	removeLabels(siteInfoWindows); // turn all labels off
		    	loadListeners(); //reload event listeners (hover to display labels)
		    } 
	    	currZoom = newZoom;
		});

		function showLabels(sIW) {
		    //loop through passed infowindows array, TURN ALL LABELS ON, remove event listeners
		    for (l in sIW) {
		        siteInfoWindows[ l ].open(map,siteMarkers[ l ]);
		        google.maps.event.addListener(siteLabelListeners[l]);
		        google.maps.event.removeListener(siteMouseoutListeners [l]); //remove mouseout event listener (marker highlight, label disappear)
		        google.maps.event.removeListener(siteMouseoverListeners [l]); //remove mouseover event listener (marker highlight, label)
		    }
		};

		function removeLabels(sIW) {
		    //loop through passed infowindows array, TURN LABELS OFF, add event listeners back
		    for (l in sIW) {
		    	siteInfoWindows[ l ].close();
		    }
		};

		<?php
			
			//======================================================================
			//The following is working code that downloads the most recent ncbt data
			//POTENTIAL FUTURE - move this to a CRON JOB that runs each night and creates static file (if it speeds loading)
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
			  echo "console.log('Connected successfully - creating markers and listeners');\n";
			};


			/* 
				create two functions, one for markers, one for labels and event listeners
				enables refreshing of label events without redrawing all markers upon map zoom
			*/

			$markers = 'function loadMarkers() {';
			$listeners = 'function loadListeners() {';

			$sql = "SELECT * FROM site_data";
			$result = $conn->query($sql);
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$slug = $row["SITESLUG"];


					$listeners .='iwContent = "<div class=\"site-popup\">' . $row['TITLE'] . '</div>";';
					$listeners .= 'siteInfoWindows["' . $slug . '"] = new InfoBox(infoboxOptions);';
					$listeners .= 'siteInfoWindows["' . $slug . '"].setContent(iwContent);';
					//$listeners .= 'console.log("is this thing on?");';
					//$listeners .= 'siteLabelListeners["' . $slug . '"] = google.maps.event.addListener(siteInfoWindows["' . $slug . '"], "click", function() {triggerInfoPanel("' . $slug . '");});';
					$listeners .= 'siteLabelListeners["' . $slug . '"] = google.maps.event.addListener(siteInfoWindows["' . $slug . '"], "click", function() {console.log("label clicked");});';
					$listeners .= 'siteMouseoverListeners["' . $slug . '"] = google.maps.event.addListener(siteMarkers["' . $slug . '"], "mouseover", function() {siteMarkers["' . $slug . '"].setIcon(highlightIcon); siteInfoWindows["' . $slug . '"].open(map,siteMarkers["' . $slug . '"]); });';
					$listeners .= 'siteMouseoutListeners["' . $slug . '"] = google.maps.event.addListener(siteMarkers["' . $slug . '"], "mouseout", function() {siteMarkers["' . $slug . '"].setIcon(siteIcon); siteInfoWindows["' . $slug . '"].close();});';
					//$test = 'google.maps.event.addListener(siteInfoWindows["' . $slug . '"], "click", function() {triggerInfoPanel("' . $slug . '");});';
					//echo "console.log('" . $test . "');";
					$markers .= 'siteMarkers["' . $slug . '"] = new google.maps.Marker({position:{lat:' . $row["LAT"] . ', lng:' . $row["LON"] . '},icon:siteIcon,map:map});';
					$markers .= 'google.maps.event.addListener(siteMarkers["' . $slug . '"], "click", function() {triggerInfoPanel("'. $slug . '");});';
				}
				//$markers .= 'console.log("loadMarkers run");};'; //close out function
				$markers .= '};'; //close out function
				echo $markers;

				//$listeners .= 'console.log("loadListeners run");};'; //close out function
				$listeners .= '};'; //close out function
				echo $listeners;
			}
			
		?>
		loadMarkers();
		loadListeners();
	});
</script>
<?php get_footer(); ?>