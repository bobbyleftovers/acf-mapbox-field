<?php
/*
Plugin Name: Advanced Custom Fields: Mapbox geoJSON
Plugin URI: https://github.com/jensjns/acf-mapbox-geojson-field
Description: Adds a map field that lets you edit geoJSON-content.
Version: 0.0.3
Author: jensnilsson
Author URI: http://jensnilsson.nu
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// 1. set text domain
// Reference: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
load_plugin_textdomain( 'acf-mapbox_geojson', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );

// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_mapbox_geojson( $version ) {

    include_once('acf-mapbox_geojson-v5.php');

}

add_action('acf/include_field_types', 'include_field_types_mapbox_geojson');

// 3. Include field type for ACF4
function register_fields_mapbox_geojson() {

    // include_once('acf-mapbox_geojson-v4.php');

}

add_action('acf/register_fields', 'register_fields_mapbox_geojson');?>