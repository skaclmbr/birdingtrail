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
	          <div class="col-8"><h3 class="modal-title text-left" id="NAME"></h3></div><!-- fix font; make smaller on smaller screens? -->
	          <div class="col-4">
	          	<button type="button" id="close-modal" class="close" data-dismiss="modal">&times;</button><br/>
	          	<div id="site-open-status" class="badge site-open-badge"></div>
	          </div>
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
				<div id="HABITATS-CARD" class="card">
  					<a href="#HABITATS" class="btn btn-primary" data-toggle="collapse">Habitats</a>
  				</div>
				<div id="HABITATS" class="collapse site-info"></div>

        <!-- Modal footer -->
        <div id="modal-footer" class="row modal-footer">
          <div id="TRAVELINFO" class="modal-footer-row"></div>
          <div id="nav-web-div" class="modal-footer-row"></div>
          <div class="col-md modal-footer-footer">
			  <div id="twitter-button" class="footer-footer-buttons"> <a id="twitter-share" href="" target="_blank"><i class="fa fa-twitter-square"></i></a></div>
<!-- 
			  <div id="facebook-button" class="footer-footer-buttons"> <a id="facebook-share" href="" target="_blank"><i class="fa fa-facebook-square"></i></a></div>
			  <div id="insta-button" class="footer-footer-buttons"> <a id="insta-share" href="" target="_blank"><i class="fa fa-instagram"></i></a></div>
			   -->
			  <div id="google-button" class="footer-footer-buttons" ><a id="GOOGLELINK" href="" target="_blank"><i class="fa fa-google"></i></a></div><!-- be sure to include link to Google Maps Page here -->
	      </div>
        </div>
	  </div>
    </div>
   </div>


	<div id="map-container" class="row">
		<!--google map canvas -->
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
	function tryGeolocation (logVisit) {
		if (typeof(logVisit)==='undefined') logVisit = true;
		if (navigator.geolocation) {
		  navigator.geolocation.getCurrentPosition(function(position) {
		  	
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
		    bLatLng = true; //set boolean to true if LatLon acquired
		    
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
		centerButton.innerHTML = "<i class='fa fa-crosshairs'></i>";
		controlUI.appendChild(centerButton);

		var stateButton = document.createElement('button');
		stateButton.className = 'btn btn-light';
		stateButton.innerHTML = "<i class='fa fa-search-minus'></i>";
		controlUI.appendChild(stateButton);

		 // Setup the click event listeners: simply set the map to Chicago.
        centerButton.addEventListener('click', function() {tryGeolocation();});
        stateButton.addEventListener('click', function() {zoomState(map);});
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

	/** Defines the Popup class. */
	function definePopupClass() {
	  /**
	   * A customized popup on the map.
	   * @param {!google.maps.LatLng} position
	   * @param {!Element} content
	   * @constructor
	   * @extends {google.maps.OverlayView}
	   */
	  Popup = function(siteslug, position, content) {
	    
	    this.position = position;
	    this.siteslug = siteslug;
 	    content.classList.add('popup-bubble-content');

	    this.anchor = document.createElement('div');
	    this.anchor.classList.add('popup-bubble-anchor');
	    this.anchor.appendChild(content);

	    // Optionally stop clicks, etc., from bubbling up to the map.
	    this.stopEventPropagation(this.siteslug);
	  };
	  // NOTE: google.maps.OverlayView is only defined once the Maps API has
	  // loaded. That is why Popup is defined inside initMap().
	  Popup.prototype = Object.create(google.maps.OverlayView.prototype);

	  /** Called when the popup is added to the map. */
	  Popup.prototype.onAdd = function() {
	    this.getPanes().floatPane.appendChild(this.anchor);
	  };

	  /** Called when the popup is removed from the map. */
	  Popup.prototype.onRemove = function() {
	    if (this.anchor.parentElement) {
	      this.anchor.parentElement.removeChild(this.anchor);
	    }
	  };

	  /** Called when the popup needs to draw itself. */
	  Popup.prototype.draw = function() {
	    var divPosition = this.getProjection().fromLatLngToDivPixel(this.position);
	    // Hide the popup when it is far out of view.
	    var display =
	        Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000 ?
	        'block' :
	        'none';

	    if (display === 'block') {
	      var newX = String(Number(divPosition.x) + 50)
	      var newY = String(Number(divPosition.y) -10)
	      this.anchor.style.left = divPosition.x  + 'px';
	      this.anchor.style.top = newY + 'px';
	    }
	    if (this.anchor.style.display !== display) {
	      this.anchor.style.display = display;
	    }
	  };

	  /** Stops clicks/drags from bubbling up to the map. */
	  Popup.prototype.stopEventPropagation = function(ss) {
	    var anchor = this.anchor;
	    anchor.style.cursor = 'pointer';

	    ['click', 'dblclick', 'contextmenu', 'wheel', 'mousedown', 'touchstart',
	     'pointerdown']
	        .forEach(function(event) {
	        	if (event == 'click') {
	        		anchor.addEventListener('click', function(e){triggerInfoPanel(ss);});
	        	} else {
		         	anchor.addEventListener(event, function(e) {e.stopPropagation();});
	    		}
	        });
	  };

	}

	jQuery(document).ready(function(){
		var bLatLng=false;
		
		//attempt to get geolocation from browser window
		tryGeolocation();	

		
		//setup class for the popup labels
		definePopupClass();

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
		jQuery("#infoPanel").on('hidden.bs.modal', function(e){

			clearModalPanel(); //on maps.js
		});
		

		var defaultLatLng = new google.maps.LatLng(35.2,-79.8); // 
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

		//setup marker definitions
		var siteMarkers = {};
		var sitePopups= {};
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


		// ==================================================================
		// ZOOM Sensitive labeling
		// This listener changes display upon zoom change - functions in map.js
		// Makes labels visible, disables hover behavior

		var zoomDisplayLabels = 11; //range from 0-19; 0 = whole world
		var currZoom = map.getZoom();
		google.maps.event.addListener(map, 'zoom_changed', function() {
		    newZoom = map.getZoom();
		    if (newZoom >= zoomDisplayLabels && currZoom<zoomDisplayLabels) { //zoomed in, crossed label zoom threshold
		    	showLabels(sitePopups); //turn all labels on, remove hover events
		    } else if (newZoom < zoomDisplayLabels && currZoom >=zoomDisplayLabels ) { //zoomed out, crossed label zoom threshold
		    	removeLabels(sitePopups); // turn all labels off
		    	loadMouseListeners(); //reload mouseout, mouseover event listeners (hover to display labels)
		    } 
	    	currZoom = newZoom;
		});

		function showLabels(sIW) {
		    //loop through passed infowindows array, TURN ALL LABELS ON, remove event listeners
		    console.log('showLabels run');
		    for (l in sIW) {
		        sitePopups[ l ].setMap(map);

		        google.maps.event.removeListener(siteMouseoutListeners [l]); //remove mouseout event listener (marker highlight, label disappear)
		        google.maps.event.removeListener(siteMouseoverListeners [l]); //remove mouseover event listener (marker highlight, label)
		    }

		};

		function removeLabels(sIW) {
		    //loop through passed infowindows array, TURN LABELS OFF, add event listeners back
		    console.log('removeLabels run');
		    for (l in sIW) {
		    	sitePopups[ l ].setMap(null);
		    }
		};

		<?php
			
			//======================================================================
			//The following is working code that downloads the most recent ncbt data
			//TODO: Rewrite this function to be included in the functions.php get_ncbt_data() function (remove duplication)
			//POTENTIAL FUTURE - move this to a CRON JOB that runs each night and creates static file (if it speeds loading)
			//======================================================================

			//Connect to database, get site data
			// get db login information
			include('db_info.php');

			$conn = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($conn->connect_error) {
			   die("console.log('Connection failed');");
			} 
			// else {
			//   echo "console.log('Connected successfully - creating markers and listeners');\n";
			// };


			/* 
				create two functions, one for markers, one for labels and event listeners
				enables refreshing of label events without redrawing all markers upon map zoom
			*/

			$markerslabels = 'function loadMarkersLabels() {console.log("loadMarkers Run!");';
			$mouselisteners = 'function loadMouseListeners() {console.log("loadListeners Run!");';

			//TRY - build remove listeners function to be run on zoom trigger

			//loop through database results, and build function
			$sql = "SELECT * FROM site_data";
			$result = $conn->query($sql);
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$slug = $row["SITESLUG"];


					//create infobox/infowindow, add content
					$markerslabels .= 'popupContent = document.createElement("div",{id:"pop-' . $slug . '",text:"' . $row['TITLE'] . '"});';
					
					$markerslabels .= 'newText = document.createTextNode("' . $row['TITLE'] . '");popupContent.appendChild(newText);popupContent.setAttribute("id","pop-' . $slug . '");';
					
					$markerslabels .= 'sitePopups["' . $slug . '"] = new Popup("' . $slug . '",new google.maps.LatLng(' . $row["LAT"] . ',' . $row["LON"] . '),popupContent);';


					//place marker on map, add listener to open modal with information
					$markerslabels .= 'siteMarkers["' . $slug . '"] = new google.maps.Marker({position:{lat:' . $row["LAT"] . ', lng:' . $row["LON"] . '},icon:siteIcon,map:map});';
					$markerslabels .= 'google.maps.event.addListener(siteMarkers["' . $slug . '"], "click", function() {triggerInfoPanel("'. $slug . '");});';

					//turns on hover behavior (highlight dot, make label visible) - desktop only
					$mouselisteners .= 'siteMouseoverListeners["' . $slug . '"] = google.maps.event.addListener(siteMarkers["' . $slug . '"], "mouseover", function() {siteMarkers["' . $slug . '"].setIcon(highlightIcon); sitePopups["' . $slug . '"].setMap(map); });';

					//turns off hover behavior (un-highlight dot, hide label) - desktop only
					$mouselisteners .= 'siteMouseoutListeners["' . $slug . '"] = google.maps.event.addListener(siteMarkers["' . $slug . '"], "mouseout", function() {siteMarkers["' . $slug . '"].setIcon(siteIcon); sitePopups["' . $slug . '"].setMap(null);});';

				} //end loop

				$markerslabels .= '};'; //close out function
				echo $markerslabels;

				$mouselisteners .= '};'; //close out function
				echo $mouselisteners;

			} //end if rows
			
		?>

		//add the labels, markers, and listeners to the map
		loadMarkersLabels();
		loadMouseListeners();
	});
</script>
<?php get_footer(); ?>