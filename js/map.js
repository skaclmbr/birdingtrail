/* ====================================================
* Run this code after the entire page loaded
*/


/* ====================================================
* This function:
*   - retrieves site detail
*   - unhides the information panel
*
*   NEED: reorganize this code to perform ajax requests asynchronously
*     Daisy chain them?
*     - ncbirdingtrail.org database
*     - google maps information
*     - ebird information
*/
var sitePlaceId;
var featureFields = ['BOATACCESS', 'BOATLAUNCH','CAMPING','FEE','HANDICAP','HIKING','HUNTING','INTERPRETIVE','PICNIC','RESTROOMS','TRAILMAPS','VIEWING','VISITOR'];

function triggerInfoPanel(slug){
    var ncbtData;

    jQuery("#infoPanel").modal("show");
    //TESTING/TODO: retrieve site data from server
    //make multiple async ajax requests, complete when finished

    // jQuery.when(
      /*
      *  put code here to execute calls to external APIs
      *  1. ncbt site information from database
      *  2. if google PlaceID, skip 3
      *  3. search for google placeID
      *  4. retrieve google place information
      *  5. populate modal!
      */

    // ).then(function(x){
    //   console.log('jquery when works!');
    // });
    // console.log(slug);
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: ajaxurl, //url for WP ajax php file, var def added to header in functions.php
        data: {
            'action': 'get_ncbt_data', //server side function
            'dbrequest': 'site_detail', //request type
            // 'slug': 'airlie-gardens',
            // 'dbrequest': 'test', //TESTING
            // 'siteslug' : 'airlie-gardens' //identifying data for the site
            'slug' : slug //identifying data for the site - for some reason this doesn't work if passed variable is 'siteslug'
        },
        success: function(data, status) {
          
          // console.log("success triggered");
          // console.log(status);
          // console.log(data);

          populateInfoPanel(data); //commented out for testing
          }, 
        error: function(jqxhr, status, exception) {
/*
          console.log("error triggered");
          console.log(status + " : " + exception);

*/        }

        //success: function(data) {ncbtData = data;}
    });
      
    /*
    ).then(function() {
      console.log(ncbtData);
      populateInfoPanel(ncbtData);
      };
    );
    */
};


/* ====================================================
* This function populates the information into the info panel
*/
function populateInfoPanel(site_data) {
    // populate infopanel with returned site data from NCBT Database
    // array with available infopanel headings

    //TITLE
    jQuery('#NAME').empty().append(site_data['TITLE']);
    
    //DESCRIPTION
    jQuery('#DESCRIPTION').empty().append(site_data['DESCRIPTION']);

    //SPECIES
    jQuery('#SPECIES').empty().append(site_data['SPECIES']);

    //HABITATS
    jQuery('#HABITATS').empty().append(site_data['HABITATS']);

    //FEATURE ICONS
    // add icons to represent features
    console.log(site_data);

/*    for (var i=0, item; item=featureFields[i]; i++) {
      //insert code here to include icons (or make visible?)
      if (site_data[item]==1){
        console.log(item);
        // jQuery('#feature-'+ item).css('display', 'inline');
        jQuery('#feature-'+ item).removeClass('f-hide');
        // jQuery('#feature-'+ item).addClass('f-show');

      } else {
        jQuery('#feature-'+ item).addClass('f-hide');

      }
    }
*/
    // loop through badgest on modal, retrieve appropriate site data to determine if to display
    jQuery('.feature-img').each(function() {
      id = jQuery(this).attr('id');
      if (site_data[id]==1) {
        jQuery(this).removeClass('f-hide');
      } else {
        jQuery(this).addClass('f-hide');
      }
    });


    // EXTWEBSITE create external website button, if exists
    if (site_data['EXTWEBSITE'].length>0) {
      webLink = jQuery ('<a/>', {
        href: site_data['EXTWEBSITE'],
        target: '_blank',
        id: 'EXTWEBSITE',
        class: 'btn btn-outline-light nav-web-buttons',
        text: 'Website'
      });
      jQuery("#nav-web-div").append(webLink);

    } else {
      console.log ("no external website");
    } 


    //ADD SOCIAL MEDIA LINKS - NEEDS WORK; add FB, Insta links
    smMessage = 'Plan on visiting ' + site_data['TITLE'] + ' on the NC Birding Trail soon!';
    /*
    * TWITTER
    * https://twitter.com/intent/tweet?text=Hello%20World&hashtags=birding,ncbirds,ncbirding,ncwildlife&url=https%3A%2F%2Fncpif.org%2F&twitterdev=ncbirdingtrail%3ANCBT
    * EXAMPLE: https://twitter.com/home?status=Just%20discovered%20that%20Anderson%20Point%20Park%20is%20on%20the%20%40ncbirdingtrail!%20%23birding%20%23ncbirding
    */
    var uri = 'https://twitter.com/intent/tweet?hashtags=birding,ncbirding,ncbirds&url=' + site_data['EXTWEBSITE'] + '&twitterdev=ncbirdingtrail&via=ncbirdingtrail&text=' + smMessage ;
    // console.log(encodeURI(uri));
    jQuery('#twitter-share').attr('href',encodeURI(uri));
    
    /*
    * FACEBOOK
    * EXAMPLE: https://twitter.com/home?status=Just%20discovered%20that%20Anderson%20Point%20Park%20is%20on%20the%20%40ncbirdingtrail!%20%23birding%20%23ncbirding
    *
    
    var fbUri = 'https://www.facebook.com/sharer.php?link=' + site_data['EXTWEBSITE'] + '&caption=' + smMessage ;
    console.log(encodeURI(uri));
    jQuery('#facebook-share').attr('href',encodeURI(fbUri));

    */

    // RETRIEVE GOOGLE PLACE ID
    //Find Place ID for Google information
    var siteLatLng = new google.maps.LatLng(site_data['LAT'],site_data['LON']);

    sitePlaceId =site_data['PLACEID']; //check to see if site ID in database
    // console.log("site place ID to pass: " + sitePlaceId);
    if (!sitePlaceId.length) { //if not, search google for it
        retrievePlaceId(site_data['TITLE'],site_data['SITESLUG'], siteLatLng ,function(results){
            //console.log("another function test: " + results);
            sitePlaceId = results;
            // console.log(results);
            if(sitePlaceId) {
              sitePlaceData = retrievePlaceData(sitePlaceId);
            } else {
              //no placeid returned...
      
            };
            //update database with PlaceID - don't need this? done in retrievePlaceData()
            // updateSiteInfo(site_data['SITESLUG'],'PLACEID',sitePlaceId);
        });
    } else {
        sitePlaceData = retrievePlaceData(sitePlaceId);

    }
    //What to do if no record found?
    //retrieve place information from Google (if place ID retrieved)
    // MAKE SURE TO PUT IN CODE HERE TO HANDLE MULTIPLE PLACE ID Responses


    /* GOOGLE DISTANCE INFO
    Get distance information from google, populate travel info
    This function calcluates the distance and travel time from the gmaps Directions Matrix
    https://developers.google.com/maps/documentation/javascript/distancematrix
    */
    if (currLatLng){ //only do this if geolocation worked!
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
          navLink = jQuery ('<a/>', {
            href: dirUrl,
            id: 'NAVIGATION',
            class: 'btn btn-outline-light nav-web-buttons',
            text: 'Navigate'
          });
          jQuery("#nav-web-div").append(navLink);

    }
}

/* 
Reset modal panel to original settings
Clear out data and collapse all elements
*/
function clearModalPanel () {
    //remove all "show" classes
    // console.log('clear modal run');
    jQuery(".modal-content.show").removeClass("show");

    jQuery('#NAME').empty();   //TITLE    
    jQuery('#DESCRIPTION').empty();    //DESCRIPTION
    jQuery('#SPECIES').empty();     //SPECIES
    jQuery('#HABITATS').empty();    //HABITATS
    jQuery('.feature-img').addClass('f-hide');    //FEATURE BADGES

    jQuery('#site-open-status').empty();
    jQuery('#site-open-status').removeClass('badge-danger badge-success');
    jQuery('#site-open-status').empty();

    jQuery('.hours').remove();

    jQuery('.site-modal-header').removeAttr("height");
    //jQuery('#modal-header-image').css({'clip':'','top':0});
    jQuery('#modal-header-image').removeAttr("src");

    jQuery('#twitter-share').removeAttr("href");

    jQuery('#EXTWEBSITE').remove();
    jQuery('#NAVIGATION').remove();

}

/* SEARCH GOOGLE FOR PLACE ID
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
            // console.log("google placeid found, updating database");
            updateSiteInfo(slug, 'PLACEID',results[0].place_id);
            rPID(results[0].place_id);
        } else {
            console.log("no google placeid found");
            rPID(null);
        }
    
    pid = returnPID;
    });
};

// RETRIEVE GOOGLE PLACE INFORMAION
function retrievePlaceData (p) {
    //pass place id, get data from Google
    // console.log('retrieve data: ' + p);

    var request = {
      placeId: p,
      fields: ['name','geometry','opening_hours','types','photos','website','permanently_closed','url']
    };

    placeDService = new google.maps.places.PlacesService(map);
    place = placeDService.getDetails(request,function (place, status) {
      if (status == google.maps.places.PlacesServiceStatus.OK) {
        populateGoogleData(place);
      } else {
        return null;
      }
    });
}

/* POPULATE MODAL
Populate the Modal with info from Google Place API
*/

function populateGoogleData(place){
  // console.log(place);
  //HOURS
  //remove all hours elements on modal    
  jQuery('#HOURS-CARD').remove();
  jQuery('#HOURS').remove();
  jQuery('#HRSLINK').remove();


  //POPULATE HOURS
  if (place['opening_hours']) {
    //console.log('adding hours elements');
    //hours exists, add appropriate buttons
    hrsCard = jQuery('<div/>',{
      class: 'card',
      id: 'HOURS-CARD'
    });
    hrsLink = jQuery('<a/>',{
      class: 'btn btn-primary',
      id: 'HRSLINK',
      'data-toggle' : 'collapse',
      href: '#HOURS',
      text: 'Hours'
    });

    hrsList = jQuery('<div/>',{
      class: 'collapse site-info',
      id: 'HOURS'
    });

    hrsCard.append(hrsLink);
    jQuery('#modal-footer').before(hrsCard);

    jQuery('#modal-footer').before(hrsList);

    //loop through daily hour elements and add to the new div
    jQuery(place['opening_hours']['weekday_text']).each(function(i,val){
        hrsDiv = jQuery ('<div/>', {
          class: 'hours',
          text: val
        });
        jQuery("#HOURS").append(hrsDiv);
    });

    // ADD APPROPRIATE OPEN BADGE
    if (place['opening_hours']['open_now']){
        jQuery("#site-open-status").append("Open Now").addClass("badge-success");
    } else {
        jQuery("#site-open-status").append("Closed Now").addClass("badge-danger");
    }
      
  }

  //POPULATE GOOGLE LINK - THIS IS REQUIRED TO USE GOOGLE SERVICES
  if (place['url']) {jQuery('#GOOGLELINK').attr('href',place['url']);};

  //RETRIEVE/DISPLAY PHOTO
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
            'slug': slug, //site slug - for some reason, using siteslug as variable causes error
            'field': f, //field to update
            'data' : d //data to insert
        },
        success: function(data) {
          // console.log(slug + ", " + f + ", " + data);
        },
        error: function(jqxhr, status, exception) {
          // console.log(status + " : " + exception);
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
