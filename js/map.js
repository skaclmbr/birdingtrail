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
var eBirdId = 'vcsnp5gci7f9';
var featureFields = ['BOATACCESS', 'BOATLAUNCH','CAMPING','FEE','HANDICAP','HIKING','HUNTING','INTERPRETIVE','PICNIC','RESTROOMS','TRAILMAPS','VIEWING','VISITOR'];

var site_data; //global holder for site data information

function triggerInfoPanel(slug){
    var ncbtData;
    clearModalPanel();

    jQuery("#infoPanel").modal("show");
    //TESTING/TODO: retrieve site data from server
    //make multiple async ajax requests, complete when finished

    // SEE EXAMPLE HERE: https://api.jquery.com/jQuery.when/ 
    // jQuery.when(
      /*
      *  put code here to execute calls to external APIs
      *  1. ncbt site information from database
      *  2. if google PlaceID in db table, skip 3
      *  3. search for google placeID
      *  4. retrieve google place information
      *  5. populate modal!
      */

    // ).then(function(x){
    //   console.log('jquery when works!');
    // });
    // console.log(slug);

    //console.log('ajax request to get ncbt site data - ' + slug);
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: ajaxurl, //url for WP ajax php file, var def added to header in functions.php
        data: {
            /* Retrieve data from external DB */
            'action': 'get_trailmgmt_data', //server side function - trailmgmt plugin
            //'action': 'get_ncbt_data', //server side function - external table
            'dbrequest': 'site_detail', //request type
            'siteslug' : slug //identifying data for the site - for some reason this doesn't work if passed variable is 'siteslug'
        },
        success: function(data, status) {

          site_data = data; //populate global variable
          populateInfoPanel(); //commented out for testing
          }, 
        error: function(jqxhr, status, exception) {

          //console.log("error retrieving ncbt data ajax call");
          //console.log(status + " : " + exception);

        }

        //success: function(data) {ncbtData = data;}
    });
      
};


function populateInfoPanel() {
// function populateInfoPanel(site_data) {
/* ====================================================
* This function populates the information into the info panel
*/
    // populate infopanel with returned site data from NCBT Database
    // array with available infopanel headings

    /* ========================================================================================
    *  POPULATE DATA FROM DATABASE
    */
    //collapse all panels
    //console.log('populating info panel');
    //console.log(site_data);

    jQuery('.collapse').removeClass('show');

    //TITLE
    jQuery('#NAME').empty().append(site_data['title']);
    
    //DESCRIPTION
    jQuery('#DESCRIPTION').empty().append(site_data['description']);

    //SPECIES
    jQuery('#SPECIES').empty().append(site_data['species']);
    jQuery('#modal-subheading-sightings').addClass('f-hide');
    jQuery('#SIGHTINGS').empty(); //clear out the sightings

    //HABITATS
    jQuery('#HABITATS').empty().append(site_data['habitats']);

    //FEATURE ICONS
    // loop through amenity icons on modal, retrieve appropriate site data to determine if to display
    jQuery('.feature-img').each(function() {
      id = jQuery(this).attr('id');
      if (site_data[id]==1) {
        jQuery(this).removeClass('f-hide');
      } else {
        jQuery(this).addClass('f-hide');
      }
    });


    // EXTWEBSITE create external website button, if exists
    if (site_data['extwebsite'].length>0) {
      webLink = jQuery ('<a/>', {
        href: site_data['extwebsite'],
        target: '_blank',
        id: 'EXTWEBSITE',
        class: 'btn btn-outline-light nav-web-buttons',
        text: 'Website'
      });
      jQuery("#nav-web-div").append(webLink);

    } else {
      //console.log ("no external website");
    } 


    /* ========================================================================================
    *  ADD SOCIAL MEDIA LINKS - NEEDS WORK; add FB, Insta links
    */
    smMessage = 'Plan on visiting ' + site_data['title'] + ' on the NC Birding Trail soon!';
    /*
    * TWITTER
    * https://twitter.com/intent/tweet?text=Hello%20World&hashtags=birding,ncbirds,ncbirding,ncwildlife&url=https%3A%2F%2Fncpif.org%2F&twitterdev=ncbirdingtrail%3ANCBT
    * EXAMPLE: https://twitter.com/home?status=Just%20discovered%20that%20Anderson%20Point%20Park%20is%20on%20the%20%40ncbirdingtrail!%20%23birding%20%23ncbirding
    */
    var uri = 'https://twitter.com/intent/tweet?hashtags=birding,ncbirding,ncbirds&url=' + site_data['extwebsite'] + '&twitterdev=ncbirdingtrail&via=ncbirdingtrail&text=' + smMessage ;
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

    //===========================================================================
    //DEFINE google lat long for W3W and Google Place info
    var siteLatLng = new google.maps.LatLng(site_data['lat'],site_data['lon']);
    //console.log(siteLatLng.lat());
    //console.log(siteLatLng.lng());

    //===========================================================================
    //WHAT3WORDS EVAL (is it in the db?)
    //console.log('about to get w3w address...');
    siteW3wWords = site_data['what3words']
    if (siteW3wWords && siteW3wWords.length>0) {
      //console.log("W3W found in db")
      populateW3wAddress(siteW3wWords);
    } else {
      //database field is empty, lookup and populate
      //console.log('W3W database field is empty, lookup and populate');
      //getW3WAddress(siteLatLng);
      var w3w_options = {
        key:'JBM1JF04',
        lang:'en'
      };

      w3w = new W3W.Geocoder(w3w_options);
      var callback = {
          onSuccess: function(data) {
              //console.log(JSON.stringify(data));
              populateW3wAddress(data.words);
              updateSiteInfo(site_data['siteslug'],'what3words',data.words);
          },
          onFailure: function(data) {
              //console.log(JSON.stringify(data));
          }
      };

      var params = {
          coords: [siteLatLng.lat(), siteLatLng.lng()]
      };

      w3w.reverse(params, callback);
    }

    /* ========================================================================================
    * RETRIEVE GOOGLE DATA
    */

    // RETRIEVE GOOGLE PLACE ID
    //Find Place ID for Google information

    sitePlaceId = site_data['placeid']; //check to see if google place ID in database
    // console.log("site place ID to pass: " + sitePlaceId);
    if (!sitePlaceId.length) { //if not, search google for it
        retrievePlaceId(site_data['title'],site_data['siteslug'], siteLatLng ,function(results){
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
        //console.log("looking for google distance info. Current position:");
        //console.log(currLatLng.lat());
        //console.log(currLatLng.lng());
        //console.log("current position = true");
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
          //console.log(status + " : ");
          //console.log(response);
          retDistDur = {'dist':dist['text'],'dur':dur['text']};
          jQuery("#TRAVELINFO").empty().append(retDistDur['dist'] + " away (" + retDistDur['dur'] + ")");
        }
    
        };


    }
    
    //ADD link to google directions
    dirUrl = mapsSelector(siteLatLng);
    navLink = jQuery ('<a/>', {
      href: dirUrl,
      id: 'NAVIGATION',
      class: 'btn btn-outline-light nav-web-buttons',
      text: 'Navigate'
    });
    jQuery("#nav-web-div").append(navLink);



} //end populateInfoPanel

function populateW3wAddress(words){
    //console.log("adding w3w data to modal - " + words);
    jQuery("#w3w-info").css('display','block');
    jQuery("#w3w-address").text(words);
    jQuery("#w3w-link").attr("href","https://w3w.co/" + words);

};


function getW3WAddress (coords){
    /* WHAT3WORDS link and info
    Get what3 words address from lat/long
    ADD TO DATABASE!!!!
    Eventually could create a large scale map of the site, with W3W addresses for POIs
    */
    // What3words key JBM1JF04
    //retrieve GeoJSON
    //console.log('get w3w address run...')
    
    w3wrevaddruri=encodeURI('https://api.what3words.com/v2/reverse?coords=' + coords.lat() + ',' + coords.lng() + '&lang=en&display=full&format=json&key=' + w3w_key); //TESTING
   //console.log(coords);
    //console.log(w3wrevaddruri);
    var settings = {
      "async": true,
      "crossDomain": true,
      "url": w3wrevaddruri,
      "method": "GET",
      "headers": {}
    }

    jQuery.ajax(settings).done(function (response) {
      //console.log(response);
      populateW3wAddress(response.words);
      updateSiteInfo(site_data['siteslug'],'what3words',response.words);


    });


  };


function clearModalPanel () {
/* 
Reset modal panel to original settings
Clear out data and collapse all elements
*/
    //remove all "show" classes
    // console.log('clear modal run');
    jQuery(".modal-content.show").removeClass("show");

    jQuery('#NAME').empty();   //TITLE    
    jQuery('#DESCRIPTION').empty();    //DESCRIPTION
    jQuery('#SPECIES').empty();     //SPECIES
    jQuery('#SIGHTINGS').empty();     //EBIRD SIGHTINGS
    jQuery('#ebird-location-link').attr('href','http://www.ebird.org');
    jQuery('#HABITATS').empty();    //HABITATS
    jQuery('.feature-img').addClass('f-hide');    //FEATURE BADGES

    jQuery('#site-open-status').empty();
    jQuery('#site-open-status').removeClass('badge-danger badge-success');
    jQuery('#site-open-status').empty();

    //remove all hours elements on modal    
    jQuery('#HOURS-CARD').remove();
    jQuery('#HOURS').remove();
    jQuery('#HRSLINK').remove();


    jQuery('.site-modal-header').removeAttr("height");
    //jQuery('#modal-header-image').css({'clip':'','top':0});
    jQuery('#modal-header-image').removeAttr("src");

    //TODO remove button entirely? - any case when there wouldn't be a link?
    jQuery('#twitter-share').removeAttr("href");

    jQuery('#EXTWEBSITE').remove();
    jQuery('#NAVIGATION').remove();

    //remove google link button at bottom
    jQuery('#google-button').remove();

    //remove W3W info
    jQuery('#w3w-info').css('display','none')
    jQuery('#w3w-link').attr('href','http://www.what3words.com');
    jQuery('#w3w-address').text('');

} //end clearModalPanel

function retrievePlaceId(placeName, slug, location, rPID){
/* SEARCH GOOGLE FOR PLACE ID
Check if Place ID exists from database,
    if not, retrieve and populate database

Then, retrieve place data from Google (hours, photos, etc. ) 
https://developers.google.com/maps/documentation/javascript/places#placeid
*/
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
            updateSiteInfo(slug, 'placeid',results[0].place_id);
            rPID(results[0].place_id);
        } else {
            //console.log("no google placeid found");
            rPID(null);
        }
    
    pid = returnPID;
    });
} //end retrievePlaceId

function retrievePlaceData (p) {
// RETRIEVE GOOGLE PLACE INFORMAION
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
} //end retrievePlaceData


function populateSightings() {
/* ====================================================================
*  GET EBIRD DATA
*  pass either locid or lat/lon, get data back from eBird
*/
// function populateSightings(site_data) {
  //get information from eBird, populate modal with recent sightings
  //console.log('site Data: ');
  //console.log(site_data);
  //check to see if location id in database record
  if (!site_data['locid']){
      // console.log("searching for location id");
      locId = getLocID(site_data['lat'],site_data['lon'], site_data['title']);
      // console.log("location ID: " + locId);
      
      site_data['locid'] = locId;
      //update database code here

  }

  //update badge link in subsection header
  if (site_data['locid']) {
    jQuery('#ebird-location-link').attr('href', 'https://ebird.org/barchart?r=' + site_data['locid'] + '&yr=all&m=');
  } else {
    jQuery('#ebird-location-link').attr('href', 'https://ebird.org');

  }
  
  var searchDist = 30; //distance from location to search
  // var searchDays = 7; //num of days back to search
  // var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
  // var months = ["Jan", "Feb", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

  //console.log("https://ebird.org/ws2.0/data/obs/geo/recent/notable?lat=" + site_data['lat'] + "&lng=" + site_data['lon'] + "&dist=" + searchDist);
  var settings = {
    "async": true,
    "crossDomain": true,
    "url": "https://ebird.org/ws2.0/data/obs/geo/recent/notable?lat=" + site_data['lat'] + "&lng=" + site_data['lon'] + "&dist=" + searchDist, //find notable sightings only
    // "url": "https://ebird.org/ws2.0/data/obs/geo/recent?lat=" + site_data['LAT'] + "&lng=" + site_data['LON'] + "&dist=" + searchDist,
    // "url": "https://ebird.org/ws2.0/data/obs/geo/recent?lat=" + site_data['LAT'] + "&lng=" + site_data['LON'] + "&dist=" + searchDist + "&back=" + searchDays,
    "method": "GET",
    "headers": {
      "X-eBirdApiToken": eBirdId
    }
  }

  jQuery.ajax(settings).done(function(r){
    //console.log(r); //TESTING
    //make two columns to display bird list
    var birdLeft = jQuery('<div/>', {class:'modal-subsection-column col-6', id:'modal-subsection-column-left'});
    var birdRight = jQuery('<div/>', {class:'modal-subsection-column col-6', id:'modal-subsection-column-right'});
    
    birdList = []; //array for storing birds seen
    count = 0; //count how many seen

    jQuery(r).each(function(i,val){

        birdText = val.comName;

        //make sure the list is unique
        if (birdList.indexOf(birdText)== -1) {
          count +=1;
          birdList.push(birdText);

          if (val.locId) {
/*          Tried to make links more relevant, but must be a separate ajax call...            
            obsDate = String(val.obsDt);
            console.log(val.locId);
            console.log(obsDate);
            console.log(obsDate.substring(0,4));
            birdURI = 'https://ebird.org/ws2.0/product/lists/' + val.locId + '/' + obsDate.substring(0,4) + '/' + obsDate.substring(5,7) + '/' + obsDate.substring(8,10) + '/?maxResults=10';
*/

            birdURI = 'https://ebird.org/barchart?r=' + val.locId + '&yr=all&m=';
          } else {
            birdURI = 'http://www.ebird.org/hotspots';
            
          }
          birdDiv = jQuery ('<div/>', {class: 'bird-sighting'});
          birdLink = jQuery('<a/>', {class: 'bird-sighting-link', text: birdText, href:birdURI,title:val.locName, target: '_blank'});
          birdDiv.append(birdLink);
  
          if (isOdd(count)) {
            birdLeft.append(birdDiv);
          } else {
            birdRight.append(birdDiv);
          }
        }
    }); //end loop through eBird response

    if (count >0) { //make sure at least one bird found, then unhide...
      jQuery('#modal-subheading-sightings').removeClass('f-hide');
      jQuery('#SIGHTINGS').append(birdLeft);
      jQuery('#SIGHTINGS').append(birdRight);
    }
  }); //end eBird ajax request


  /* Location bar chart URI examples:
  *  https://ebird.org/barchart?r=L189480&yr=all&m=
  *
  *  //multiple location example
  *  https://ebird.org/barchart?byr=1900&eyr=2018&bmo=1&emo=12&r=L216915,L3800262,L1248065
  */


} //end populateSightings

function isOdd(num){return num %2; }

function getLocID (lat, lng, name) {
  // check ebird for closest location id to site (look for up to 3km away)
  //console.log('searching for eBird Location ID');
  var settings = {
    "async": true,
    "crossDomain": true,
    "url": "https://ebird.org/ws2.0/ref/hotspot/geo?lat=" + lat + "&lng=" + lng + "&dist=3&fmt=json",
    "method": "GET",
    "headers": {
      "X-eBirdApiToken": eBirdId
    }
  }

  jQuery.ajax(settings).done(function (response) {
    // eList = jQuery.parseJSON(response);
    // console.log(eList);
    var mVal = 0;
    var mName = '';
    var mLocId = '';
    for (var i = 0; i<response.length; i++){
      tMVal = stringMatch(name, response[i].locName);
      if (tMVal>mVal) {
        mVal = tMVal;
        mName = response[i].locName;
        mLocId = response[i].locId;
      }
    } //end loop through ajax response

    // console.log("matched " + name + " TO " + mName + " : " + mLocId)
    updateSiteInfo(site_data['siteslug'],'locid', mLocId);
    //console.log('locid call:');
    //console.log(response);
    return mLocId;
  }); //end eBird ajax request
} //end getLocID

/*
function getEbirdNearby (lat,lng) {
  //input location info and type
  // lat == latitude
  // lng == longitude
  // return => json object with list of nearby observations

  var searchDist = 5; //distance from location to search
  var searchDays = 14; //num of days back to search
  var results;
  console.log ("searching eBird records: " + lat + "," + lng);

  var settings = {
    "async": true,
    "crossDomain": true,
    "url": "https://ebird.org/ws2.0/data/obs/geo/recent?lat=" + lat + "&lng=" + lng + "&dist=" + searchDist,
    "method": "GET",
    "headers": {
      "X-eBirdApiToken": eBirdId
    }
  }
  console.log("results1: " + results);
  jQuery.when(jQuery.ajax(settings)).done(function(r){
    console.log('results2:');
    console.log(r);
    return r;

  });

*/

/*  jQuery.ajax(settings).done(function (response) {
    results = response;
    console.log('response: ');
    console.log(response);
    console.log('results2: ');
    console.log(results);
  });
  )
}
*/


function populateGoogleData(place){
/* POPULATE MODAL
Populate the Modal with info from Google Place API
*/
  // console.log(place);
  //HOURS

  //POPULATE HOURS
  if (place['opening_hours']) {
    //console.log('adding hours elements');
    //hours exists, add appropriate buttons
    hrsCard = jQuery('<div/>',{class: 'card', id: 'HOURS-CARD'});
    hrsLink = jQuery('<a/>',{class: 'btn btn-primary',id: 'HRSLINK','data-toggle' : 'collapse',href: '#HOURS',text: 'Hours'});

    hrsList = jQuery('<div/>',{class: 'collapse site-info', id: 'HOURS'});

    hrsCard.append(hrsLink);
    jQuery('#modal-footer').before(hrsCard);
    jQuery('#modal-footer').before(hrsList);

    //loop through daily hour elements and add to the new div
    jQuery(place['opening_hours']['weekday_text']).each(function(i,val){
        hrsDiv = jQuery ('<div/>', {class: 'hours', text: val});
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
    gButton = jQuery('<div/>', {id: 'google-button', class: 'footer-footer-buttons'});
    gLink = jQuery('<a/>', {id:'GOOGLELINK',href:place['url'],target:'_blank'});
    gLink.html('<i class="fa fa-google"></i>');
    gButton.append(gLink);

    jQuery('.modal-footer-footer').append(gButton);


  //RETRIEVE/DISPLAY PHOTO
  var photos = place.photos;
  if (photos) {
      mWidth = jQuery('.site-modal-header').outerWidth();
      mHeightNum = 180;
      mHeight = String(mHeightNum) + 'px';
      jQuery('.site-modal-header').css('height',mHeight);
      jQuery('#modal-header-image').attr('src',photos[0].getUrl({'maxWidth': mWidth}));
      jQuery('#modal-header-image').css('clip','rect(0px, ' + mWidth + 'px,' + mHeight + ',0px)');
  }

} //end populateGoogleData


function updateSiteInfo(slug, f, d) {
/* =====================================================================
* UPDATE back end database with parameters passed
*/
    /* 
    slug = SITESLUG
    f = Field to be updated
    d = Data to update field to
    FUTURE - enable field array and data array, so that multiple fields can be updated 
    */
    //console.log("updating database for " + slug + " field: " + f + " data: " + d);

    data = jQuery.ajax ({
        type:"POST",
        url: ajaxurl, //url for WP ajax php file, var def added to headeirn in functions.php
        data: {
/* INFO FROM OLD, SEPARATE TABLE
            'action': 'get_ncbt_data', //server side function
            'dbrequest': 'update_site_info', //add to the visit table
*/
            'action': 'get_trailmgmt_data',
            'dbrequest': 'update_site_field',
            'slug': slug, //site slug - for some reason, using siteslug as variable causes error
            'field': f, //field to update
            'data' : d //data to insert
        },
        success: function(data, status) {
            //console.log(status);
            //console.log(data);
        },
        error: function(jqxhr, status, exception) {
            //console.log(status);
            //console.log(exception);
            //console.log(jqxhr);
        }
    });
} //end updateSiteInfo


function mapsSelector(gLatLng) {
/* =====================================================================
* determines if the platform is apple or android, provides nav link appropriately
*/
  //Pass navigator platform, lat lng in google format
  //link format - https://www.google.com/maps/dir/22.7683707,-99.4103449/35.6805556,-78.6275/@24.0908076,-102.5559874,6.06z
  // link with just destination location - baseUrl = "://maps.google.com/maps?daddr=" + gLatLng.lat() + "," + gLatLng.lng()+ "&amp;ll=";

  //baseUrl = "://maps.google.com/maps/dir/" + gLatLng.lat() + "," + gLatLng.lng()+ "/"+currLatLng.lat()+","+currLatLng.lng()+"/@"+gLatLng.lat() + "," + gLatLng.lng();
  baseUrl = "://maps.google.com/maps?saddr=Current%20Location&daddr=" + gLatLng.lat() + "," + gLatLng.lng() + "&amp;11=";
  //console.log (baseUrl);
  if /* if we're on iOS, open in Apple Maps */
    ((navigator.platform.indexOf("iPhone") != -1) || 
     (navigator.platform.indexOf("iPad") != -1) || 
     (navigator.platform.indexOf("iPod") != -1))
    return "maps" + baseUrl;
else /* else use Google */
    return "https" + baseUrl;
} //end mapSelector

function stringMatch (s1,s2){
/* ====================================================
* calculates the closeness of two strings - larger values are closer matches
* counts the number of matching words
*/
  a1 = s1.split(" ");
  mVal = 0

  for (var i = 0; i<a1.length; i++){
    if (s2.search(a1[i])>=0) {mVal +=1;}
  }

  return mVal;
} //end stringMatch

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
