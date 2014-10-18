/**
 * Ajax Request Page for the Loading of the Feature Maps
 *
 * @author: Alex Stillwagon
 * @package Alex's Feature Maps
 * Author URI: http://alexstillwagon.com
 * @version: 1.2.4
 *
 * Note that jQuery is loaded in safe mode for use in WP ( i.e. no '$' var. Instead the full 'jQuery' var. )
 *
 */

// Load jQueryUI Tabs for Map Page
jQuery(document).ready(function ($) {
    $("#asd-map-wrap").tabs();
//    $('#asd-map-place ').spin('map', '#444');
    window.setTimeout( addDirections, 1100 );
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
    e.preventDefault();
    var index = jQuery(this).attr('data-load');
    jQuery(this).spin('map_tab', '#fff');
    jQuery(this).parent().toggleClass('loading');
    showGroup(index);
});

function showGroup(index) {
    var el = jQuery('#' + index);
    jQuery('#maps-tabs').find('li').removeClass('active');
    jQuery(el).parent().addClass('active');

    // The meat of the code - Loads .post ( jQuery Ajax call ) and sends variables via POST. Returns Data Type: JSON
    jQuery.post(MapAjax.asd_feature_map_ajaxurl, { cat: index, asd_feature_map_nonce: MapAjax.asd_feature_map_nonce, action: 'asd_feature_map_action' }, function (mapdata) {
        var $item = jQuery('#map-tabs').find('li.loading');
        jQuery('#asd-map-place').spin( false );
        $item.find('a').spin( false );
        $item.toggleClass('loading');
        //loads returned data into the map
        maplace.Load({
            locations: mapdata.locations,
            view_all_text: mapdata.title,
            type: mapdata.type,
            force_generate_controls: true
        });
    }, 'json');
}

function addDirections() {
    var $container = jQuery('#asd-map-controls').find('li').not(':first-of-type');
    var $is_apple = jQuery('.directions').attr('data-os');

    if ( $is_apple ) {
        $container.append(function(){
            var $directions = jQuery(this).find('.directions');
            var $link = $directions.attr('data-name');
            var $lat = $directions.attr('data-lat');
            var $lng = $directions.attr('data-lng');
            var $address = $directions.attr('data-addr');
        return '<p><a href="' + $link + '">View Stylist</a></p><p><a href="http://maps.apple.com/?ll=' + $lat + ',' + $lng + '&daddr=' + $address + '">Get Directions</a></p>';
        });
    }
    else {
        $container.append(function(){
            var $directions = jQuery(this).find('.directions');
            var $link = $directions.attr('data-name');
            var $lat = $directions.attr('data-lat');
            var $lng = $directions.attr('data-lng');

           return '<p><a href="' + $link + '">View Stylist</a></p><p><a href="https://www.google.com/maps/place/' + $lat + ',' + $lng + '">Get Directions</a></p>';
        });
    }

}