<?php
/**
 * Plugin Name: 3D Printing
 * Plugin URI: https://accuratedigitalsolutions.com/
 * Description: This plugin will calculate the estimated volume, printing time, and cost
 * Version: 1.1
 * Author: ADS
 * Author URI: https://accuratedigitalsolutions.com/
 * License: GPLv2 or later
 * Text Domain: ADS
 */
const STL_PLUGIN_DIR = __DIR__;
define("STL_PLUGIN_URL", plugins_url('/', __FILE__));
define("DEFAULT_LAYER_HEIGHTS", [0.1, 0.2, 0.3]);


require_once(STL_PLUGIN_DIR . '/admin/admin.php');
require_once(STL_PLUGIN_DIR . '/backend/stl_handler.php');
require_once(STL_PLUGIN_DIR . '/backend/stl_model.php');
require_once(STL_PLUGIN_DIR . '/frontend/frontend.php');

add_action('admin_enqueue_scripts', 'ads_stl_admin_scripts');
/**
 * This method renders the scripts and styles to be used on the admin side
 * @return void
 */
function ads_stl_admin_scripts(): void {
  wp_enqueue_style('ads-stl-style', STL_PLUGIN_URL . 'assets/css/custom.css', false, '1.4');
  wp_enqueue_script('ads-stl-js', STL_PLUGIN_URL . 'assets/js/admin.js', false, '1.4', true);
}


add_action('wp_enqueue_scripts', 'ads_stl_client_scripts');
/**
 * this method renders the scripts and styles to be used on the client side
 * @return void
 */
function ads_stl_client_scripts(): void {
  wp_enqueue_style('ads-site-stl-style', STL_PLUGIN_URL . 'assets/css/site-styles.css', false, '1.4');
  wp_enqueue_style('ads-upload-stl-style', STL_PLUGIN_URL . 'assets/css/upload-form.css', false, '1.4');
  wp_enqueue_script('ads-upload-stl-js', STL_PLUGIN_URL . 'assets/js/upload-form.js', false, '1.5', true);
  wp_localize_script('ads-upload-stl-js', 'frontend_ajax', array('ajaxURL' => admin_url('admin-ajax.php')));
}
