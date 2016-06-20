<?php
/**
 * Plugin Name:Alex's Feature Maps
 * Description: Load locations on Google&trade; Maps using Advanced Custom Fields, Maplace.js, and jQuery.
 * @author: Alex Stillwagon
 * @package Alex's Feature Maps
 * Author URI: http://alexstillwagon.com
 * @version: 1.2.9
 * Requires at least: 3.8
 * Tested up to: 3.9.2
 *
 * Text Domain: asd_feature_map
 *
 * @thanks to Daniele Moraschi for Maplace.js
 * Learn more at http://maplacejs.com/
 * Maplace.js is Released under the MIT license.
 * GitHub at https://github.com/danielemoraschi/Maplace.js
 *
 * @thanks to Elliot Condon for Advanced Custom Fields
 * Learn more at http://www.advancedcustomfields.com/
 * Advanced Custom Fields License: GPLv2 or later
 * Advanced Custom Fields License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * GitHub at https://github.com/elliotcondon/acf/
 *
 * @thanks to Felix Gnass for Spin.js
 * Learn more at http://fgnass.github.io/spin.js/
 * Spin.js is Licensed under the MIT license
 */

/*
|--------------------------------------------------------------------------
| CONSTANTS
|--------------------------------------------------------------------------
*/
if ( !defined( 'AFM_BASE_FILE' ) ) {
    define( 'AFM_BASE_FILE', __FILE__ );
}
if ( !defined( 'AFM_BASE_DIR' ) ) {
    define( 'AFM_BASE_DIR', dirname( AFM_BASE_FILE ) );
}
if ( !defined( 'AFM_PLUGIN_URL' ) ) {
    define( 'AFM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/*
|--------------------------------------------------------------------------
| INCLUDES
|--------------------------------------------------------------------------
*/

// Check if user has already installed ACF Pro
if (
    ! in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ||
    ! in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
    ) {

    // Embed Advanced Custom Fields Plugin
    include_once ( 'includes/advanced-custom-fields/acf.php' );

    // Hide ACF from the Admin Side
    // define( 'ACF_LITE', true );

}

/*
|--------------------------------------------------------------------------
| DEFINE THE CUSTOM POST TYPE
|--------------------------------------------------------------------------
*/

add_action( 'init', 'asd_feature_map', 0 );
/**
 * Setup the Custom Post Type
 */
function asd_feature_map () {

    $labels = array (
        'name' => _x( 'Feature Maps', 'Post Type General Name', 'asd_feature_map' ),
        'singular_name' => _x( 'Feature Map', 'Post Type Singular Name', 'asd_feature_map' ),
        'menu_name' => __( 'Feature Maps', 'asd_feature_map' ),
        'parent_item_colon' => __( 'Parent Place:', 'asd_feature_map' ),
        'all_items' => __( 'All Places', 'asd_feature_map' ),
        'view_item' => __( 'View Place', 'asd_feature_map' ),
        'add_new_item' => __( 'Add New Place', 'asd_feature_map' ),
        'add_new' => __( 'New Place', 'asd_feature_map' ),
        'edit_item' => __( 'Edit Place', 'asd_feature_map' ),
        'update_item' => __( 'Update Place', 'asd_feature_map' ),
        'search_items' => __( 'Search Places', 'asd_feature_map' ),
        'not_found' => __( 'No Places found', 'asd_feature_map' ),
        'not_found_in_trash' => __( 'No Places in Trash', 'asd_feature_map' ),
    );
    $rewrite = array (
        'slug' => 'featured-places',
        'with_front' => false,
        'pages' => true,
        'feeds' => true,
    );
    $supports = array ( 'title', 'revisions', );

    $args = array (
        'label' => __( 'asd_feature_map', 'asd_feature_map' ),
        'description' => __( 'Feature Map', 'asd_feature_map' ),
        'labels' => $labels,
        'supports' => $supports,
        'taxonomies' => array ( 'map_category' ),
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 20,
        'menu_icon' => AFM_PLUGIN_URL . 'images/menu_icon.png',
        'can_export' => false,
        'has_archive' => false,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'rewrite' => $rewrite,
        'capability_type' => 'post',
    );

    register_post_type( 'asd_feature_map', $args );
}

/*
|--------------------------------------------------------------------------
| CREATE CUSTOM MESSAGES
|--------------------------------------------------------------------------
*/

add_filter( 'post_updated_messages', 'asd_feature_map_messages' );
/**
 * Create custom messages for users when adding, editing, updating or deleting
 * @param $messages
 * @return mixed
 */
function asd_feature_map_messages ( $messages ) {
    global $post, $post_ID;
    $messages['asd_feature_map'] = array (
        0 => '',
        1 => sprintf( __( 'Place updated. <a href="%s">View Item</a>', 'asd_feature_map' ), esc_url( get_permalink( $post_ID ) ) ),
        2 => esc_html__( 'Place updated.', 'asd_feature_map' ),
        3 => esc_html__( 'Place deleted.', 'asd_feature_map' ),
        4 => esc_html__( 'Place updated.', 'asd_feature_map' ),
        5 => isset( $_GET['revision'] ) ? sprintf( __( 'Place restored to revision from %s', 'asd_feature_map' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __( 'Place published. <a href="%s">View Place</a>', 'asd_feature_map' ), esc_url( get_permalink( $post_ID ) ) ),
        7 => esc_html__( 'Place saved.', 'asd_feature_map' ),
        8 => sprintf( __( 'Place submitted. <a target="_blank" href="%s">Preview Place</a>', 'asd_feature_map' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
        9 => sprintf( __( 'Place scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Place</a>', 'asd_feature_map' ), date_i18n( __( 'M j, Y @ G:i', 'asd_feature_map' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
        10 => sprintf( __( 'Place draft updated. <a target="_blank" href="%s">Preview Place</a>', 'asd_feature_map' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
    );

    return $messages;
}

/*
|--------------------------------------------------------------------------
| FLUSH REWRITES
|--------------------------------------------------------------------------
*/

register_activation_hook( __FILE__, 'asd_feature_map_rewrite_flush' );
/**
 * Flush Rewrite Rules on Activation
 */
function asd_feature_map_rewrite_flush () {
    asd_feature_map();
    // ATTENTION: This is *only* done during plugin activation hook
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}

/*
|--------------------------------------------------------------------------
| ENQUEUE SCRIPTS AND STYLES
|--------------------------------------------------------------------------
*/

add_action( 'wp_enqueue_scripts', 'asd_feature_map_enqueue' );
/**
 * Enqueue Front Side Scripts and Styles
 */
function asd_feature_map_enqueue () {
    wp_enqueue_style( 'asd_feature_map_css', plugins_url( 'asd_feature_map.dev.css', __FILE__ ) );
    wp_register_script( 'asd_google_maps', '//maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.7', 'jQuery', NULL, true );
    wp_register_script( 'asd_maplace', AFM_PLUGIN_URL . 'js/maplace.js', 'jQuery', NULL, true );
    wp_register_script( 'asd_spin', AFM_PLUGIN_URL . 'js/spin.js', 'jQuery', NULL, true );

    // LOAD jQueryUI Tabs
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_style( 'asd-map-jquery-ui' );

    // LOAD Map JS Files
    wp_enqueue_script( 'asd_google_maps' );
    wp_enqueue_script( 'asd_maplace' );
    wp_enqueue_script( 'asd_map_jquery_custom' );
    wp_enqueue_script( 'asd_spin' );

    // embed the javascript file that makes the AJAX request
    wp_enqueue_script( 'asd_feature_map_ajax', plugin_dir_url( __FILE__ ) . 'js/jquery.ajax.feature-map.js', array ( 'jquery' ), NULL, true );

    // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
    wp_localize_script( 'asd_feature_map_ajax', 'MapAjax',
        array (
            'asd_feature_map_ajaxurl' => admin_url( 'admin-ajax.php' ),
            'asd_feature_map_nonce' => wp_create_nonce( 'asd_feature_map_nonce' )
        )
    );
}

/**
 * Enqueue Admin Scripts & Styles
 */
add_action( 'admin_enqueue_scripts', 'asd_feature_map_admin_styles' );
/**
 * Load Admin Styles
 */
function asd_feature_map_admin_styles () {
    if ( is_admin() ) {
        echo '<script type="text/javascript">var mapPath = "' . AFM_PLUGIN_URL . '";</script>';
        // Load css for Admin side only
        wp_enqueue_style( 'asd_feature_map_css', plugins_url( 'asd_feature_map.dev.css', __FILE__ ) );
        wp_enqueue_script( 'asd_feature_map_admin_js', plugin_dir_url( __FILE__ ) . 'js/jquery.admin.js', array ( 'jquery' ), NULL, true );
    }
}

/*
|--------------------------------------------------------------------------
| ADMIN SECTION
|--------------------------------------------------------------------------
*/

add_filter( 'manage_edit-asd_feature_map_columns', 'asd_feature_map_edit_columns' );
/**
 * Add Custom Columns to the Admin List Page
 * @param $columns
 * @return array
 */
function asd_feature_map_edit_columns ( $columns ) {
    $columns = array (
        'cb' => '<input type="checkbox" />',
        'asd_feature_place_icon' => __( '' ),
        'title' => __( 'Place' ),
        'asd_feature_map_bubble' => __( 'Bubble' ),
        'asd_feature_map_subhead' => __( 'Description' ),
        'asd_map_category' => __( 'Category' ),
        'date' => __( 'Date' )
    );

    return $columns;
}

add_action( 'manage_asd_feature_map_posts_custom_column', 'asd_feature_map_manage_columns', 10, 2 );
/**
 * Populate the Custom Admin Columns with Data
 * @param $column
 * @param $post_id
 */
function asd_feature_map_manage_columns ( $column, $post_id ) {
    global $post;

    switch ( $column ) {
        /* Map Icon column. */
        case 'asd_feature_place_icon' :
            $map_icon = get_field( 'asd_feature_place_icon' );
            if ( $map_icon ) {

                //asd_feature_map_upload_icon
                if ( $map_icon == 'custom' ) {
                    $place = get_field( 'asd_feature_map_upload_icon' );
                    $icon = $place['sizes']['asd_feature_map_icon'];
                }
                else {
                    //Display the Place Icon
                    $icon = AFM_PLUGIN_URL . '/images/icons/' . $map_icon . '.png';
                }
            }
            else {
                $icon = AFM_PLUGIN_URL . '/images/icons/marker.png';
            }

            echo '<img src="' . $icon . '" alt="map icon" height="16" width="16"/>';

            break;

        /* Subhead column. */
        case 'asd_feature_map_subhead' :
            /* Get the genres for the post. */
            $subhead = get_field( 'asd_feature_map_subhead' );
            /* If no sku is found, output a default message. */
            if ( empty( $subhead ) ) {
                echo '';
            }
            else { /* If there is an sku. */
                echo $subhead;
            }
            break;

        /* Bubble column. */
        case 'asd_feature_map_bubble' :
            /* Get the genres for the post. */
            $bubble = get_field( 'asd_feature_map_bubble' );
            /* If no sku is found, output a default message. */
            if ( empty( $bubble ) ) {
                echo '';
            }
            else { /* If there is an sku. */
                echo $bubble;
            }
            break;

        /* Category column. */
        case 'asd_map_category' :
            /* Get the genres for the post. */
            $map_category = get_the_terms( $post->ID, 'asd_map_category' );
            /* If no sku is found, output a default message. */
            if ( !$map_category ) {
                echo '';
            }
            else { /* If there is an sku. */

                $array_count = count( $map_category );
                $i = 1;
                foreach ( $map_category as $term ) {
                    echo $term->name;
                    if ( $array_count > $i ) {
                        echo ', ';
                    }
                    $i++;
                }

            }
            break;

        default :
            break;
    }
}


//add_action('admin_menu', 'brdesign_enable_pages');
//
//function brdesign_enable_pages() {
//    add_submenu_page('edit.php?post_type=asd_feature_map', 'Custom Post Type Admin', 'Custom Settings', 'edit_posts', basename(__FILE__), 'custom_function');
//}

//if( function_exists('acf_add_options_page') ) {
//
//    acf_add_options_sub_page(array(
//        'page_title' 	=> 'Feature Maps Settings',
//        'menu_title'	=> 'Settings',
//        'parent_slug'	=> 'edit.php?post_type=asd_feature_map',
//        'capability'	=> 'edit_posts',
//        'redirect'		=> false
//    ));
//
//}

/*
|--------------------------------------------------------------------------
| TEMPLATES
|--------------------------------------------------------------------------
*/

/**
 * Get the custom template if is set by User Theme
 * @param $template
 * @return mixed|void
 */
function asd_feature_map_get_template ( $template ) {

    // Get the template slug
//    $template_slug = rtrim( $template, '.php' );
//    $template = $template_slug . '.php';

    // Check if a custom template exists in the theme folder, if not, load the plugin template file
    if ( $theme_file = locate_template( array ( 'feature-map/' . $template ) ) ) {
        $file = $theme_file;
    }
    else {
        $file = AFM_BASE_DIR . '/templates/' . $template;
    }

    return $file;
}

//add_filter( 'template_include', 'asd_feature_map_template_chooser' );
///**
// * Displays Template for Archive page of Custom Post Type (CPT)
// * @param $template
// * @return mixed|void
// */
//function asd_feature_map_template_chooser( $template ) {
//
//    // For all other CPT
//    if ( get_post_type() != 'asd_feature_map' ) {
//        return $template;
//    }
//
//    // Else use custom template
//    if ( is_single() ) {
//        return asd_feature_map_get_template( 'single-place' );
//    }
//
//
//}

/*
|--------------------------------------------------------------------------
| SHORTCODE
|--------------------------------------------------------------------------
*/
// Setup Shortcode Handler
function asd_feature_map_shortcode_handler () {
    $shortcode_template = asd_feature_map_get_template( 'shortcode-feature-map.php' );

    ob_start();
    /** @noinspection PhpIncludeInspection */
    include $shortcode_template;
    $result_string = ob_get_contents();
    ob_end_clean();

    return $result_string;
}

//Register Shortcode
add_shortcode( 'feature-map', 'asd_feature_map_shortcode_handler' );

/*
|--------------------------------------------------------------------------
| CUSTOM MAP CATEGORY TAXONOMY
|--------------------------------------------------------------------------
*/

if ( !function_exists( 'asd_map_category' ) ) {

// Register Custom Map Category Taxonomy
    function asd_map_category () {

        $labels = array (
            'name' => _x( 'Categories', 'Taxonomy General Name', 'asd_map_category' ),
            'singular_name' => _x( 'Category', 'Taxonomy Singular Name', 'asd_map_category' ),
            'menu_name' => __( 'Categories', 'asd_map_category' ),
            'all_items' => __( 'All Categories', 'asd_map_category' ),
            'parent_item' => __( 'Parent Category', 'asd_map_category' ),
            'parent_item_colon' => __( 'Parent Category:', 'asd_map_category' ),
            'new_item_name' => __( 'New Category', 'asd_map_category' ),
            'add_new_item' => __( 'Add Category', 'asd_map_category' ),
            'edit_item' => __( 'Edit Category', 'asd_map_category' ),
            'update_item' => __( 'Update Category', 'asd_map_category' ),
            'separate_items_with_commas' => __( 'Separate Categories with commas', 'asd_map_category' ),
            'search_items' => __( 'Search Categories', 'asd_map_category' ),
            'add_or_remove_items' => __( 'Add or remove Category', 'asd_map_category' ),
            'choose_from_most_used' => __( 'Choose from the most used Category', 'asd_map_category' ),
            'not_found' => __( 'No Categories Found', 'asd_map_category' ),
        );
        $rewrite = array (
            'slug' => 'map_category',
            'with_front' => true,
            'hierarchical' => false,
        );
        $args = array (
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'query_var' => 'asd_map_category',
            'rewrite' => $rewrite,
        );
        register_taxonomy( 'asd_map_category', 'asd_feature_map', $args );
        register_taxonomy_for_object_type( 'asd_map_category', 'asd_feature_map' );

    }

    // Register Taxonomy
    add_action( 'init', 'asd_map_category', 0 );
}


add_action( 'init', 'asd_feature_map_set_default_category' );
/**
 * Create a Default General Category to start
 */
function asd_feature_map_set_default_category () {

    if ( !term_exists( 'General', 'asd_map_category' ) ) {
        wp_insert_term( 'General', 'asd_map_category' );
    }
}

/*
|--------------------------------------------------------------------------
| ASD FEATURE MAP CUSTOM FUNCTIONS
|--------------------------------------------------------------------------
*/

/**
 * Display navigation to next/previous pages when applicable
 * From _s (underscores) More info at http://www.underscores.me
 * @param $nav_id
 */
function asd_feature_map_content_nav ( $nav_id ) {
    global $wp_query, $post;

    // Don't print empty markup on single pages if there's nowhere to navigate.
    if ( is_single() ) {
        $previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
        $next = get_adjacent_post( false, '', false );

        if ( !$next && !$previous ) {
            return;
        }
    }

    // Don't print empty markup in archives if there's only one page.
    if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) ) {
        return;
    }

    $nav_class = ( is_single() ) ? 'post-navigation' : 'paging-navigation';

    echo '<nav id="' . esc_attr( $nav_id ) . '" class="' . $nav_class . '">';

    if ( is_single() ) { // navigation links for single posts
        previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'asd_feature_map' ) . '</span> %title' );
        next_post_link( '<div class="nav-next">%link</div>', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'asd_feature_map' ) . '</span>' );
    }

    echo '</nav><!-- #' . esc_html( $nav_id ) . ' -->';
}

/**
 * Cleans text
 * @param $string
 * @return mixed|string
 */
function asd_feature_map_clean_classes ( $string ) {
    //Lower case everything
    $string = strtolower( $string );
    //Make alphanumeric (removes all other characters)
    $string = preg_replace( "/[^a-z0-9_\\s-]/", "", $string );
    //Clean up multiple dashes or whitespaces
    $string = preg_replace( "/[\\s-]+/", " ", $string );
    //Convert whitespaces and underscore to dash
    $string = preg_replace( "/[\\s_]/", "-", $string );

    return $string;
}


add_action( 'init', 'asd_feature_map_add_image_sizes' );
/**
 * Add Extra Image Size to WP
 */
function asd_feature_map_add_image_sizes () {
    add_image_size( 'asd_feature_map_icon', 32, 32, true );
}

/*
|--------------------------------------------------------------------------
| AJAX REQUEST
|--------------------------------------------------------------------------
*/

// if both logged in and not logged in users can send this AJAX request,
// add both of these actions, otherwise add only the appropriate one
add_action( 'wp_ajax_nopriv_asd_feature_map_action', 'asd_feature_map_ajax' );
add_action( 'wp_ajax_asd_feature_map_action', 'asd_feature_map_ajax' );

/**
 * Process the Ajax Request sent from
 */
function asd_feature_map_ajax () {
    global $post, $is_iphone, $is_safari;
    // Check Security
    if ( !isset( $_POST['asd_feature_map_nonce'] ) || !wp_verify_nonce( $_POST['asd_feature_map_nonce'], 'asd_feature_map_nonce' ) ) {
        die( 'You have no stinking badgers!' );
    }

    // Make sure a Category is Set
    if ( isset( $_POST['cat'] ) ) {
        $get_category = $_POST['cat'];
    }
    else {
        $get_category = 'general';
    }

    // Since this file will be output through an existent post/page we will setup a separate WP Query Loop
    $map_args = array (
        'post_type' => 'asd_feature_map',
        'orderby' => 'name', //TODO ADD custom ordering method
        'order' => 'ASC',
        'tax_query' => array ( //Limit to Map Category send via POST
            array (
                'taxonomy' => 'asd_map_category',
                'field' => 'slug',
                'terms' => $get_category
            )
        )
    );

    // the Query
    $query_map = new WP_Query( $map_args );

    if ( $query_map->have_posts() ) {

        // Setup some primary vars
        $count = $query_map->post_count;
        $place_counter = 1;
        $map_category = get_term_by( 'slug', $get_category, 'asd_map_category' );
        $map_category = $map_category->name;

        // Start the JSON string here
        echo '{"title": "' . $map_category . '", "type": "marker", "locations": [';

        // Loop
        while ( $query_map->have_posts() ) {

            $query_map->the_post();

            // Setup some Loop vars
            $place = get_fields();
            
            // Lat / Lon Position
            $lat = $place['asd_feature_map_location']['lat'];
            $lng = $place['asd_feature_map_location']['lng'];
            $directions = $place['asd_feature_map_show_link'];


            // Map Bubble Display
            $bubble = '';
            if ( $place['asd_feature_map_bubble'] ) {
                $bubble = esc_attr( $place['asd_feature_map_bubble'] );
                $show_bubble = 'true';
            }
            else {
                $show_bubble = 'false';
            }

            // Place Name and Subhead
            $link = site_url( '/map/' ) . '?location=' . $post->post_name;
            if ( $place['asd_feature_map_subhead'] ) {
                $place_name = '<h3>' . get_the_title() . '</h3><p>' . $place['asd_feature_map_subhead'] . '</p>';
            }
            else {
                $place_name = '<h3>' . get_the_title() . '</h3>';
            }

            if ( $directions ) {
                if ( $directions == 'map' ) {
                    $place_name .= '<p class=\'directions\' data-lat=\'' . $lat . '\' data-lng=\'' . $lng . '\'  data-name=\'' . $link . '\'';

                    if ( $is_iphone || $is_safari ) {
                        $place_name .= ' data-os=\'true\'';
                    }

                    else {
                        $place_name .= ' data-os=\'false\'';
                    }

                    $place_name .= '></p>';
                }

                if ( $directions == 'directions' ) {
                    $place_name .= '<p class=\'directions\' data-lat=\'' . $lat . '\' data-lng=\'' . $lng . '\' data-addr=\'' . str_replace( ' ', '+', $place['asd_feature_map_location']['address'] ) . '\' data-name=\'' . $link . '\'';


                    if ( $is_iphone || $is_safari ) {
                        $place_name .= ' data-os=\'true\'';
                    }

                    else {
                        $place_name .= ' data-os=\'false\'';
                    }

                    $place_name .= '></p>';
                }
            }

            // Marker Icon
            if ( $place['asd_feature_place_icon'] ) {

                //asd_feature_map_upload_icon
                if ( $place['asd_feature_place_icon'] == 'custom' ) {
                    $icon = $place['asd_feature_map_upload_icon']['sizes']['asd_feature_map_icon'];
                }
                else {
                    $icon = AFM_PLUGIN_URL . 'images/icons/' . $place['asd_feature_place_icon'] . '.png';
                }

            }
            else {
                // Default Icon
                $icon = AFM_PLUGIN_URL . 'images/icons/marker.png';
            }

            // Map Zoom Setting - From Google Maps API / 20 is closer ( zoomed in ), 1 is further ( zoomed out )
            if ( $place['asd_feature_map_zoom'] ) {
                $zoom = $place['asd_feature_map_zoom'];
            }
            else {
                // Default Zoom
                $zoom = 12;
            }

            // Create JSON Data' . $place_name . '
            echo '{
                "lat": ' . $lat . ',
                "lon": ' . $lng . ',
                "zoom": ' . $zoom . ',
                "show_infowindow": ' . $show_bubble . ',
                "title": "' . $place_name . '",
                "html": "' . $bubble . '",
                "icon": "' . $icon . '"';
            if ( $place_counter < $count ) { // More Places
                echo '}, ';
            }
            else { // If only 1 Location or Last Location the in Array
                echo '}';
            }
            $place_counter++;
        }
        // Close JSON Data
        echo ']';
        if ($count == 1) {
            echo', "map_options": {"zoom": ' . $zoom . '}';
        }
        echo '}';
    }
    // Reset the query because we are probably within the Main WP Loop
    wp_reset_query();

    // Required for the correct handling of Ajax by WP. Otherwise it will give a response of 0 for the Ajax data
    die();
}

/*
|--------------------------------------------------------------------------
| ADVANCED CUSTOM FIELDS DEFAULT FIELDS
|--------------------------------------------------------------------------
*/

if ( function_exists( "register_field_group" ) ) {
    register_field_group( array (
        'id' => 'acf_feature-maps',
        'title' => 'Feature Maps',
        'fields' => array (
            array (
                'key' => 'field_533a518f5f908',
                'label' => 'Location',
                'name' => 'asd_feature_map_location',
                'type' => 'google_map',
                'instructions' => 'Search for an address, city, state, point of interest, etc...',
                'center_lat' => '39.011902',
                'center_lng' => '-98.484246499999985',
//                'zoom' => '12',
            ),
            array (
                'key' => 'field_533ce8c59024f',
                'label' => 'Place Icon',
                'name' => 'asd_feature_place_icon',
                'type' => 'select',
                'instructions' => 'Select an icon for the place on the map. Select "custom" to upload a file.',
                'choices' => array (
                    'marker' => 'Marker',
                    'custom' => 'Custom Icon ...',
                    'airplane' => 'Airplane',
                    'asian-bowl' => 'Asian Bowl',
                    'beer' => 'Beer',
                    'bicycle' => 'Bicycle',
                    'bookmark' => 'Bookmark',
                    'bottle' => 'Bottle',
                    'boy' => 'Boy',
                    'branch' => 'Branch',
                    'bread' => 'Bread',
                    'briefcase' => 'Briefcase',
                    'bus' => 'Bus',
                    'butterfly' => 'Butterfly', // "Castle, why is the butterfly blue?"
                    'cake' => 'Cake',
                    'calendar' => 'Calendar',
                    'camera' => 'Camera',
                    'campfire' => 'Campfire',
                    'car' => 'Car',
                    'check' => 'Check',
                    'coffee' => 'Coffee',
                    'compass' => 'Compass',
                    'connection' => 'Connection',
                    'cupcake' => 'Cupcake',
                    'dollar' => 'Dollar',
                    'fish' => 'Fish',
                    'flag' => 'Flag',
                    'flower' => 'Flower',
                    'fork-knife' => 'Fork &amp; Knife',
                    'fork-spoon' => 'Fork &amp; Spoon',
                    'gauge' => 'Gauge',
                    'gear' => 'Gear',
                    'girl' => 'Girl',
                    'globe' => 'Globe',
                    'grill' => 'Grill',
                    'group-people' => 'Group of People',
                    'heart' => 'Heart',
                    'heather' => 'Heather',
                    'house' => 'House',
                    'jewel' => 'Jewel',
                    'key' => 'Key',
                    'lab' => 'Lab',
                    'lightbulb' => 'Light Bulb',
                    'lightening' => 'Lightening',
                    'lock' => 'Lock',
                    'lollypop' => 'Lollypop',
                    'map' => 'Map',
                    'medical-case' => 'Medical Case',
                    'microphone' => 'Microphone',
                    'molecule' => 'Molecule',
                    'music' => 'Music',
                    'paper-airplane' => 'Paper Airplane',
                    'peppermint' => 'Peppermint',
                    'pizza' => 'Pizza',
                    'pina-colada' => 'Pina Colada',
                    'price-tag' => 'Price Tag',
                    'popsicle' => 'Popsicle',
                    'fancy-pop' => 'Fancy Popsicle',
                    'radio-tower' => 'Radio Tower',
                    'rocket' => 'Rocket',
                    'school-bus' => 'School Bus',
                    'scooter' => 'Scooter',
                    'shield' => 'Shield',
                    'ship' => 'Ship',
                    'shopping-bag' => 'Shopping Bag',
                    'shopping-cart' => 'Shopping Cart',
                    'signpost' => 'Signpost',
                    'single-person' => 'Single Person',
                    'spatula' => 'Spatula',
                    'star' => 'Star',
                    'subway' => 'Subway',
                    'target' => 'Target',
                    'ticket' => 'Ticket',
                    'train' => 'Train',
                    'video-camera' => 'Video Camera',
                    'water-drop' => 'Water Drop',
                    'weight' => 'Weight',
                    'wifi' => 'Wifi',
                    'wine-bottle' => 'Wine Bottle',
                    'wine-glass' => 'Wine Glass',
                    'x' => 'X Marks the Spot',
                ),
                'default_value' => 'marker',
                'allow_null' => 1,
                'multiple' => 0,
            ),
            array (
                'key' => 'field_533db2679d09f',
                'label' => 'Select Icon',
                'name' => 'asd_feature_map_upload_icon',
                'type' => 'image',
                'instructions' => 'Size: 32px x 32px',
                'required' => 1,
                'conditional_logic' => array (
                    'status' => 1,
                    'rules' => array (
                        array (
                            'field' => 'field_533ce8c59024f',
                            'operator' => '==',
                            'value' => 'custom',
                        ),
                    ),
                    'allorany' => 'all',
                ),
                'save_format' => 'object',
                'preview_size' => 'asd_feature_map_icon',
                'library' => 'all',
            ),
            array (
                'key' => 'field_533ce93790250',
                'label' => 'Map Zoom',
                'name' => 'asd_feature_map_zoom',
                'type' => 'select',
                'instructions' => 'Please select a Map Zoom Level from 1 (large area) to 20 (maximum detail)',
                'choices' => array (
                    1 => '1 - The Entire World',
                    2 => 2,
                    3 => '3 - Hemisphere',
                    4 => 4,
                    5 => 5,
                    6 => '6 - Texas',
                    7 => '7 - Big Enough for the others States',
                    8 => 8,
                    9 => 9,
                    10 => '10 - Rhode Island',
                    11 => 11,
                    12 => '12 - City',
                    13 => 13,
                    14 => '14 - Downtown',
                    15 => 15,
                    16 => 16,
                    17 => '17 - Two City Blocks with Buildings',
                    18 => 18,
                    19 => 19,
                    20 => '20 - Sure, go ahead and name the Pigeons',
                ),
                'default_value' => 12,
                'allow_null' => 0,
                'multiple' => 0,
            ),
            array (
                'key' => 'field_533b53a59c7f8',
                'label' => 'Info Bubble',
                'name' => 'asd_feature_map_bubble',
                'type' => 'text',
                'instructions' => 'Enter the Text to appear in the pop-up info bubble',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_535aa858a86ea',
                'label' => 'Show Map Link?',
                'name' => 'asd_feature_map_show_link',
                'type' => 'select',
                'instructions' => 'Select if you would like to show a link to an external map on Google Maps, Get Directions (includes instructions to create directions), or not display a link.',
                'choices' => array (
                    'false' => 'Don\'t Show',
                    'map' => 'Show Map',
                    'directions' => 'Show Directions',
                ),
                'default_value' => 'false : Don\'t Show',
                'allow_null' => 0,
                'multiple' => 0,
            ),
            array (
                'key' => 'field_533b53f212fcd',
                'label' => 'Description',
                'name' => 'asd_feature_map_subhead',
                'type' => 'text',
                'instructions' => 'Text to appear below the Title, ex 1234 Main St., Anytown, AL 10001',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'asd_feature_map',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array (
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array (),
        ),
        'menu_order' => 0,
    ) );
}
