/**
 * Ajax Request Page for the Loading of the Feature Maps
 *
 * @author: Alex Stillwagon
 * @package Alex's Feature Maps
 * Author URI: http://alexstillwagon.com
 * @version: 1.3.0
 *
 * Note that jQuery is loaded in safe mode for use in WP ( i.e. no '$' var. Instead the full 'jQuery' var. )
 *
 */

// Load jQueryUI Tabs for Map Page
jQuery(document).ready(function ($) {
    $("#asd-map-wrap").tabs();
    $('#asd-map-place ').spin('map', '#777');
});

// Setup the Maplace
var maplace = new Maplace({
    map_div: '#asd-map-place',
    controls_div: '#asd-map-controls',
    controls_type: 'list',
    controls_on_map: false,
    view_all: true,
    view_all_text: 'All Places'
});

// Click to load the selected Map Category Tab
jQuery('#map-tabs').find('a').click(function (e) {
    var index = jQuery(this).attr('data-load');
    jQuery(this).spin('map_tab', '#fff');
    jQuery(this).parent().toggleClass('loading');
    showGroup(index);
});

function showGroup(index) {
    var el = jQuery('#' + index);
    jQuery('#maps-tabs').find('li').removeClass('active');
    jQuery(el).parent().addClass('active');

    // Loads post ( jQuery Ajax call ) and sends variables via POST. Returns Data Type: JSON
    jQuery.post(MapAjax.asd_feature_map_ajaxurl, { cat: index, asd_feature_map_nonce: MapAjax.asd_feature_map_nonce, action: 'asd_feature_map_action' }, function (mapdata) {
        var $item = jQuery('#map-tabs').find('li.loading');
        jQuery('#asd-map-place ').spin(false);
        $item.find('a').spin(false);
        $item.toggleClass('loading');
        var options = "";
        if (mapdata.hasOwnProperty('map_options')) {
            options = {
                set_center: mapdata.map_options.set_center,
                zoom: mapdata.map_options.zoom
            };
        }

        //loads returned data into the map
        maplace.Load({
            locations: mapdata.locations,
            view_all_text: mapdata.title,
            type: mapdata.type,
            force_generate_controls: true,
            map_options: options
        });
        // Success Function
        addDirections();
    }, 'json');
}

/**
 * Adds Map it and Get Directions link to Places if checked
 * @return string
 */
function addDirections() {
    var $container = jQuery('#asd-map-controls').find('li');
    var $is_apple = jQuery('.directions').attr('data-os');

    // Checks if iOS device or Apple Safari browser
    if ($is_apple == 'true') { // Load Apple Maps App
        $container.append(function () {
            var $directions = jQuery(this).find('a').not('#ullist_a_all').find('.directions');
            //var $link = $directions.attr('data-name');
            var $lat = $directions.attr('data-lat');
            var $lng = $directions.attr('data-lng');
            var $address = $directions.attr('data-addr');

            if ($lat) { // If Location is set
                if ($address) { // Show Directions
                    return '<p><a href="http://maps.apple.com/?ll=' + $lat + ',' + $lng + '&daddr=' + $address + '" class="button">Get Directions</a></p>';
                }
                else { // Show Place
                    return '<p><a href="http://maps.apple.com/?ll=' + $lat + ',' + $lng + '"  class="button">Map It</a></p>';
                }

            }
            else { // If no Location Set - Show None
                return false;
            }
        });
    }
    // If any other browser - Load Google
    else {
        $container.append(function () { // Load Apple Maps App
            var $directions = jQuery(this).find('.directions');
            //var $link = $directions.attr('data-name');
            var $lat = $directions.attr('data-lat');
            var $lng = $directions.attr('data-lng');
            var $address = $directions.attr('data-addr');
            if ($lat) { // If Location is set
                if ($address) { // Show Directions
                    return '<p><a href="https://www.google.com/maps/dir/' + $address + '/@' + $lat + ',' + $lng + '"  class="button">Get Directions</a></p>';
                }
                else { // Show Place
                    return '<p><a href="https://www.google.com/maps/place/' + $lat + ',' + $lng + '"  class="button">Map It</a></p>';
                }
            }
            else { // If no Location Set - Show None
                return false;
            }
        });
    }

}