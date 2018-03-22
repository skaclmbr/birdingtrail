

/* ====================================================
* This function:
*   - retrieves site detail
*   - unhides the information panel
*/

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

function clearInfoPanel(){
    //place code to clear out info panel values
};

/* ====================================================
* This function populates the information into the info panel
*/
function populateInfoPanel(site_data) {
    // populate infopanel with returned site data
    // array with available infopanel headings
    var siteLat;
    var siteLng;
    jQuery.each(site_data,function(i, val) {
        switch(i){
            case 'BOATACCESS':
                break;
            case 'BOATLAUNCH':
                break;
            case 'CAMPING':
                break;
            case 'FEE':
                break;
            case 'HANDICAP':
                break;
            case 'HIKING':
                break;
            case 'HUNTING':
                break;
            case 'INTERPRETIVE':
                break;
            case 'PICNIC':
                break;
            case 'RETROOMS':
                break;
            case 'TRAILMAPS':
                //insert code for amenity icons here
                //console.log("amenities");
                break;
            case 'COORDS':
                //map_window_div
                //jQuery("<a/>", ).html("#map_window_div");
                //jQuery("<a href="http://maps.google.com/maps?daddr=34.215,-77.82944444" target="_blank"><img src="http://maps.googleapis.com/maps/api/staticmap?center=34.215,-77.82944444&amp;zoom=8&amp;size=200x200&amp;maptype=terrain&amp;markers=icon:http://www.ncbirdingtrail.squarespace.com/storage/img/map_dot.png%7C34.215,-77.82944444&amp;sensor=false"></a>");
                break;
            case 'LAT':
                siteLat = val;
                break;
            case 'LON':
                siteLon = val;
                //do nothing
                break;
            case 'EXTWEBSITE':
                jQuery("#EXTWEBSITE").attr("href", val);
                //console.log(val);
                break;
            default:
                jQuery("#"+i).empty().append(val);
        }

    });
    /*
    Get distance information from google, populate travel info
    This function calcluates the distance and travel time from the gmaps Directions Matrix
    https://developers.google.com/maps/documentation/javascript/distancematrix
    */
    siteLatLng = new google.maps.LatLng(siteLat,siteLon);

    var service = new google.maps.DistanceMatrixService();
    var response = service.getDistanceMatrix(
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
      console.log (dirUrl);
      jQuery("#NAVIGATION").empty().append("Navigate here").attr('href',dirUrl);

}

/* =====================================================================
* determines if the platform is apple or android, provides nav link appropriately
*/

function mapsSelector(gLatLng) {
  //Pass navigator platform, lat lng in google format
  //link format - https://www.google.com/maps/dir/22.7683707,-99.4103449/35.6805556,-78.6275/@24.0908076,-102.5559874,6.06z
  // link with just destination location - baseUrl = "://maps.google.com/maps?daddr=" + gLatLng.lat() + "," + gLatLng.lng()+ "&amp;ll=";

  baseUrl = "://maps.google.com/maps/dir/" + gLatLng.lat() + "," + gLatLng.lng()+ "/"+currLatLng.lat()+","+currLatLng.lng()+"/@"+gLatLng.lat() + "," + gLatLng.lng();
  console.log (baseUrl);
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
        "featureType": "all",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#7c93a3"
            },
            {
                "lightness": "-10"
            }
        ]
    },
    {
        "featureType": "administrative.country",
        "elementType": "geometry",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "administrative.country",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#a0a4a5"
            }
        ]
    },
    {
        "featureType": "administrative.province",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#62838e"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#dde3e3"
            }
        ]
    },
    {
        "featureType": "landscape.man_made",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#3f4a51"
            },
            {
                "weight": "0.30"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "poi.attraction",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "poi.business",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi.government",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi.park",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "poi.place_of_worship",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi.school",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi.sports_complex",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "all",
        "stylers": [
            {
                "saturation": "-100"
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#bbcacf"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "lightness": "0"
            },
            {
                "color": "#bbcacf"
            },
            {
                "weight": "0.50"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "labels.text",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "road.highway.controlled_access",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#ffffff"
            }
        ]
    },
    {
        "featureType": "road.highway.controlled_access",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#a9b4b8"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "labels.icon",
        "stylers": [
            {
                "invert_lightness": true
            },
            {
                "saturation": "-7"
            },
            {
                "lightness": "3"
            },
            {
                "gamma": "1.80"
            },
            {
                "weight": "0.01"
            }
        ]
    },
    {
        "featureType": "transit",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#a3c7df"
            }
        ]
    }
];

