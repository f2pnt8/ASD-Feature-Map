<?php
/**
 * Template for displaying the primary map page via Shortcode [feature-map]
 *
 * This template can be overridden by coping it to [your-theme-folder]/asd_feature_map/shortcode-feature-map.php
 *
 * @author: Alex Stillwagon
 * @package Alex's Feature Maps
 * Author URI: http://alexstillwagon.com
 * @version: 1.3.4
 * @updated 23 Mar 2017
 */

// Exit if file is accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Get Map Categories to setup Tabs
$map_cat_args = array (
	'post_status' => 'publish' ,
	'orderby'     => 'name' ,
	'order'       => 'ASC' ,
	'hide_empty'  => true,
);

// Load Map Categories into an Array
$map_cats = get_terms ( 'asd_map_category' , $map_cat_args );

// Get a list of array keys
$cat_count = array_keys ( $map_cats );

// Get the first key number to use as the Map Place index
$cat_index = ( array_shift ( array_values ( $cat_count ) ) );

?>

<div id="asd-map-wrap">
	<ul id="map-tabs">
		<?php // CREATE TABS
		foreach ( $map_cats as $key => $value ) {
			echo '<li><a href="javascript:void(0)" data-load="' . esc_attr( $value->slug ). '" id="' . esc_attr( $value->slug ) . '" title="' . esc_attr( $value->name ) . '">' . esc_attr( $value->name ) . '</a></li>';
		} ?>
	</ul>

	<div id="asd-map-container">

		<?php
		if ( get_field ( 'map_height' , 'option' ) ) {
			$map_height = get_field ( 'map_height' , 'option' ) . get_field ( 'map_height_type' , 'option' );
		}
		else {
			$map_height = '400px';
		} ?>

		<div id="asd-map-place" class="gmap" style="height: <?php echo esc_attr( $map_height ); ?>"></div>
		<div id="asd-map-controls"></div>

		<script type="text/javascript">
			/**
			 * Load a Default Tab when the Page first Loads.
			 * @default  Load the first element of the $map_cats Array.
			 */
			jQuery(document).ready(function () {
				showGroup('<?php echo esc_js( $map_cats[ $cat_index ]->slug ); ?>');
			})
		</script>

	</div><!-- #asd-map-container  -->

</div><!--  #asd-map-wrap  -->