/**
 * JS for the Admin Edit Pages
 *
 * @author: Alex Stillwagon
 * @package Alex's Feature Maps
 * Author URI: http://alexstillwagon.com
 * @version: 1.3.0
 */
jQuery(document).ready(function ($) {

    /**
     * Set General Category Overrides ||||||||||||||||||||||||||||||||||||||||||||||||||||||||
     * @type {*|jQuery}
     */

    // Find the Containing Table
    var $table = $('.edit-tags-php.taxonomy-asd_map_category').find('table.wp-list-table');

    // Remove the Delete Option from the General Category
    $table.find('span.view a[href*="/map_category/general/"]').parent().parent().find('span.delete').remove();

    // Find the Bulk Edit Checkbox
    var $editcheck = jQuery('.check-column');

    // Remove the Checkbox
    if ( $editcheck.find('label').text() == 'Select General') {
        $editcheck.find('input').remove();
        $editcheck.find('label').remove();
    }

    // Set the Map Category to 'General' automatically for all New Places
    var $checkbox = $('#asd_map_categorychecklist');

    // If on Edit Place Page
    if ( $checkbox.length ) {

        // Check if any Category is already chosen
        var atLeastOneIsChecked = $checkbox.find(':checkbox:checked').length > 0;

        // If no Category is already chosen
        if ( ! atLeastOneIsChecked ) {

            // Loop through Category Checkboxes
            $checkbox.find('label').each(function() {

                // Find General category
                if ( $( this ).text() == ' General' ) {

                    // Choose the 'General' Category
                    $( this ).find('input').prop( "checked", true );
                }
            });
        }
    }

    /**
     * Map Place Icon Display ||||||||||||||||||||||||||||||||||||||||||||||||||||||||
     * @type {*|jQuery|HTMLElement}
     */

    // Find the icon container
    //var $iconDiv = $('[data-field_name="asd_feature_place_icon"]');
    var $iconDiv = $('[data-name="asd_feature_place_icon"]');

    // Add <div> to display icons
    $iconDiv.append('<div id="asd-icon-preview"></div>');

    var $iconPreview = $('#asd-icon-preview');

    // Set the icon based on the <select>
    var $link = $iconDiv.find('select').val();

    // Check if a custom icon is used
    if ($link == 'custom') {

        // Set link var to custom image src
        $link = $('#acf-asd_feature_map_upload_icon').find('img.acf-image-image').attr('src');

        // Add <img> to the display <div>
        $iconPreview.html('<img src="' + $link + '" alt="map icon" />');
    }
    else { // General, not custom, icon used

        // Add <img> to the display <div>
        $iconPreview.html('<img src="' + mapPath + 'images/icons/' + $link + '.png" alt="map icon" />');
    }


/**
 * Function to Display
 */

$iconDiv.find('select').change(function () {
    var $link = $(this).val();

    if ($link == 'custom') {
        $link = $('[data-name="asd_feature_map_upload_icon"]').find('img[data-name="image"]').attr('src');

        if ($link) {
           $('#asd-icon-preview').html('<img src="' + $link + '" alt="map icon" height="32" width="32" />');
        }
        else {
            $('#asd-icon-preview').html('<img src="' + mapPath + '/images/icons/custom.png" alt="map icon" />');
        }

    }
    else {
        $('#asd-icon-preview').html('<img src="' + mapPath + '/images/icons/' + $link + '.png" alt="map icon" />');
    }

});

});