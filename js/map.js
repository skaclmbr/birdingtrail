/* ====================================================
* Run this code after the entire page loaded
*/


/* ====================================================
* This function:
*   - retrieves site detail
*   - unhides the information panel
*/
var sitePlaceId;
function triggerInfoPanel(slug){

    jQuery("#infoPanel").modal("show");
    //retrieve site data from server
    data = jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: ajaxurl, //url for WP ajax php file, var def added to header in functions.php
        data: {
            'action': 'get_ncbt_data', //server side function
            'dbrequest': 'site_detail', //request type
            'siteslug' : slug //identifying data for the site
        },
        success: function(data) {populateInfoPanel(data);}
    });
};


/* ====================================================
* This function populates the information into the info panel
*/
function populateInfoPanel(site_data) {
    // populate infopanel with returned site data
    // array with available infopanel headings
    //console.log(site_data);
    //clearModalPanel();
    //TITLE
    jQuery('#NAME').empty().append(site_data['TITLE']);
    
    //DESCRIPTION
    jQuery('#DESCRIPTION').empty().append(site_data['DESCRIPTION']);

    //SPECIES
    jQuery('#SPECIES').empty().append(site_data['SPECIES']);

    //HABITATS
    jQuery('#HABITATS').empty().append(site_data['HABITATS']);

    //Find Place ID for Google information
    var siteLatLng = new google.maps.LatLng(site_data['LAT'],site_data['LON']);

    //console.log(site_data['PLACEID']);
    //console.log(sitePlaceId);
    sitePlaceId =site_data['PLACEID']; 
    if (sitePlaceId == null) {
        retrievePlaceId(site_data['TITLE'],site_data['SITESLUG'], siteLatLng ,function(results){
            console.log("another function test: " + results);
            sitePlaceId = results;
            sitePlaceData = retrievePlaceData(sitePlaceId);
            //update database with PlaceID
            updateSiteInfo(site_data['SITESLUG'],'PLACEID',sitePlaceId);
        });
    } else {
        sitePlaceData = retrievePlaceData(sitePlaceId);
        console.log('didnt look for placeid');
    }//What to do if no record found?
    //retrieve place information from Google (if place ID retrieved)
    // MAKE SURE TO PUT IN CODE HERE TO HANDLE MULTIPLE PLACE ID Responses

    //External Website    
    jQuery("#EXTWEBSITE").attr("href", site_data['EXTWEBSITE']);

    /*
    Get distance information from google, populate travel info
    This function calcluates the distance and travel time from the gmaps Directions Matrix
    https://developers.google.com/maps/documentation/javascript/distancematrix
    */

    var distService = new google.maps.DistanceMatrixService();
    var response = distService.getDistanceMatrix(
      {
        origins: [currLatLng],
        destinations: [siteLatLng],
        travelMode: 'DRIVING',
        unitSystem: google.maps.UnitSystem.IMPERIAL,
        avoidHighways: false,
        avoidTolls: false,
      }, callback);

    function callback(response, status) {
      if (status !== 'OK') {
            alert('Error was: ' + status);
          } else {
      var dist = response['rows'][0]['elements'][0]['distance'];
      var dur = response['rows'][0]['elements'][0]['duration'];
      retDistDur = {'dist':dist['text'],'dur':dur['text']};
      jQuery("#TRAVELINFO").empty().append(retDistDur['dist'] + " away (" + retDistDur['dur'] + ")");
    }

    };
      //ADD link to google directions
      dirUrl = mapsSelector(siteLatLng);
      //console.log (dirUrl);
      jQuery("#NAVIGATION").empty().append("Navigate here").attr('href',dirUrl);

}

/* 
Reset modal panel to original settings
Clear out data and collapse all elements
*/
function clearModalPanel () {
    //remove all "show" classes
    jQuery(".modal-content.show").removeClass("show");

    jQuery('#NAME').empty();   //TITLE    
    jQuery('#DESCRIPTION').empty();    //DESCRIPTION
    jQuery('#SPECIES').empty();     //SPECIES
    jQuery('#HABITATS').empty();    //HABITATS

    jQuery('#site-open-status').empty();
    jQuery('#site-open-status').removeClass('badge-danger badge-success');
    jQuery('#site-open-status').empty();

    jQuery('.hours').remove();

    jQuery('.site-modal-header').removeAttr("height");
    //jQuery('#modal-header-image').css({'clip':'','top':0});
    jQuery('#modal-header-image').removeAttr("src");

    jQuery('#twitter-share').removeAttr("href");

}

/*
Check if Place ID exists from database,
    if not, retrieve and populate database

Then, retrieve place data from Google (hours, photos, etc. ) 
https://developers.google.com/maps/documentation/javascript/places#placeid
*/
function retrievePlaceId(placeName, slug, location, rPID){
    /* code here to retrieve place ID from Google using Place Name */
    var request = {
        location: location,
        radius: '10', //search real close around coords!
        query: 'Google ' + placeName
    };

    var placeService = new google.maps.places.PlacesService(map);
    placeService.textSearch(request, function(results, status, returnPID ){
        // Checks that the PlacesServiceStatus is OK, and adds a marker
        // using the place ID and location from the PlacesService.
        if (status == google.maps.places.PlacesServiceStatus.OK) {
            updateSiteInfo(slug, 'PLACEID',results[0].place_id);
            rPID(results[0].place_id);
        } else {
            rPID(null);
        }
    
    pid = returnPID;
    });
};

function retrievePlaceData (p) {
    //pass place id, get data from Google
    console.log('retrieve data: ' + p);

    var request = {placeId: p};

    placeDService = new google.maps.places.PlacesService(map);
    place = placeDService.getDetails(request,function (place, status) {
      if (status == google.maps.places.PlacesServiceStatus.OK) {
        populateModal(place);
      } else {
        return null;
      }
    });
}

/*
Populate the Modal with info from Google Place API
*/
function populateModal(place){
    console.log(place);
    //populate Hours
    if (place['opening_hours']) {
        jQuery(place['opening_hours']['weekday_text']).each(function(i,val){
            console.log(val)
            jQuery("#HOURS").append('<div class="hours">' + val + '</div>');
        });
        if (place['opening_hours']['open_now']){
            jQuery("#site-open-status").append("Open Now").addClass("badge-success");
        } else {
            jQuery("#site-open-status").append("Closed Now").addClass("badge-danger");
        }
        
    }
    //populate link
    if (place['url']) {jQuery('#GOOGLELINK').attr('href',place['url']);};

    //retrieve/display photo
    
    var photos = place.photos;
    if (photos) {
        mWidth = jQuery('.site-modal-header').outerWidth();
        mHeightNum = 120;
        mHeight = String(mHeightNum) + 'px';
        /* ATTEMPTING TO SHOW MIDDLE OF IMAGE
        gImgHeight = photos[0]['height'] * (mWidth/photos[0]['width']); //calculate scaled height, used to position image
        hImgTopLeft = Math.round((gImgHeight - mHeightNum)/2);
        console.log(gImgHeight);
        console.log('rect(' + hImgTopLeft + 'px, ' + mWidth + 'px,' + mHeight + ',0px)');
        console.log(photos[0]['height']);
        console.log(photos[0]['width']);
        console.log(hImgTopLeft);
        console.log(mWidth);
        jQuery('#modal-header-image').css({'clip':'rect(' + hImgTopLeft + 'px, ' + mWidth + 'px,' + mHeight + ',0px)', 'top':'-'+gImgHeight});
        console.log(String(photos[0].getUrl({'maxWidth': 35, 'maxHeight': 35})) );
        */
        //jQuery('.modal-title-background').css('width',mWidth);
        jQuery('.site-modal-header').css('height',mHeight);
        jQuery('#modal-header-image').attr('src',photos[0].getUrl({'maxWidth': mWidth}));
        jQuery('#modal-header-image').css('clip','rect(0px, ' + mWidth + 'px,' + mHeight + ',0px)');
    }

    //Social Media Links

    //TWITTER
    // https://twitter.com/intent/tweet?text=Hello%20World&hashtags=birding,ncbirds,ncbirding,ncwildlife&url=https%3A%2F%2Fncpif.org%2F&twitterdev=ncbirdingtrail%3ANCBT
    var uri = "https://twitter.com/intent/tweet?text=" + place['name'] + '&hashtags=birding,ncbirding,ncbirds&url=' + place['website'] + '&twitterdev=ncbirdingtrail';
    console.log(encodeURI(uri));
    jQuery('#twitter-share').attr('href',encodeURI(uri));
    
}


/* =====================================================================
* UPDATE back end database with parameters passed
*/
function updateSiteInfo(slug, f, d) {
    /* 
    slug = SITESLUG
    f = Field to be updated
    d = Data to update field to
    FUTURE - enable field array and data array, so that multiple fields can be updated 
    */

    //console.log('update site table: ' + f + ' : '  + d);
    data = jQuery.ajax ({
        type:"POST",
        url: ajaxurl, //url for WP ajax php file, var def added to headeirn in functions.php
        data: {
            'action': 'get_ncbt_data', //server side function
            'dbrequest': 'update_site_info', //add to the visit table
            'siteslug': slug, //site slug
            'field': f, //field to update
            'data' : d //data to insert
        },
        success: function(data) {
          console.log(data);
        }

    });
};



/* =====================================================================
* determines if the platform is apple or android, provides nav link appropriately
*/

function mapsSelector(gLatLng) {
  //Pass navigator platform, lat lng in google format
  //link format - https://www.google.com/maps/dir/22.7683707,-99.4103449/35.6805556,-78.6275/@24.0908076,-102.5559874,6.06z
  // link with just destination location - baseUrl = "://maps.google.com/maps?daddr=" + gLatLng.lat() + "," + gLatLng.lng()+ "&amp;ll=";

  baseUrl = "://maps.google.com/maps/dir/" + gLatLng.lat() + "," + gLatLng.lng()+ "/"+currLatLng.lat()+","+currLatLng.lng()+"/@"+gLatLng.lat() + "," + gLatLng.lng();
  //console.log (baseUrl);
  if /* if we're on iOS, open in Apple Maps */
    ((navigator.platform.indexOf("iPhone") != -1) || 
     (navigator.platform.indexOf("iPad") != -1) || 
     (navigator.platform.indexOf("iPod") != -1))
    return "maps" + baseUrl;
else /* else use Google */
    return "https" + baseUrl;
}


/* ====================================================
* This variable defines the colors and formatting of the map
*/

var ncbtMapStyle = [
  {
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#ebe3cd"
      }
    ]
  },
  {
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#523735"
      }
    ]
  },
  {
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#f5f1e6"
      }
    ]
  },
  {
    "featureType": "administrative",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#c9b2a6"
      }
    ]
  },
  {
    "featureType": "administrative.land_parcel",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "administrative.land_parcel",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#dcd2be"
      }
    ]
  },
  {
    "featureType": "administrative.land_parcel",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#ae9e90"
      }
    ]
  },
  {
    "featureType": "administrative.neighborhood",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "landscape.natural",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#dfd2ae"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#dfd2ae"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "labels.text",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#93817c"
      }
    ]
  },
  {
    "featureType": "poi.park",
    "elementType": "geometry.fill",
    "stylers": [
      {
        "color": "#a5b076"
      }
    ]
  },
  {
    "featureType": "poi.park",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#447530"
      }
    ]
  },
  {
    "featureType": "road",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#f5f1e6"
      }
    ]
  },
  {
    "featureType": "road",
    "elementType": "labels",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "road.arterial",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#fdfcf8"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#f8c967"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#e9bc62"
      }
    ]
  },
  {
    "featureType": "road.highway.controlled_access",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#e98d58"
      }
    ]
  },
  {
    "featureType": "road.highway.controlled_access",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#db8555"
      }
    ]
  },
  {
    "featureType": "road.local",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#806b63"
      }
    ]
  },
  {
    "featureType": "transit.line",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#dfd2ae"
      }
    ]
  },
  {
    "featureType": "transit.line",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#8f7d77"
      }
    ]
  },
  {
    "featureType": "transit.line",
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#ebe3cd"
      }
    ]
  },
  {
    "featureType": "transit.station",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#dfd2ae"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "geometry.fill",
    "stylers": [
      {
        "color": "#b9d3c2"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "labels.text",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#92998d"
      }
    ]
  }
];
