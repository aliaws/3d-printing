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
const PRINTING_ROOT_DIR = __DIR__;

require_once(PRINTING_ROOT_DIR . '/const.php');
require_once(PRINTING_ROOT_DIR . '/backend/stl_model.php');
include_once PRINTING_ROOT_DIR . '/file.php';

function save_printer_properties(): void {
  $data = validate_printer_properties_form();
  ob_start();
  include_once(PRINTING_PLUGIN_FRONTEND_BASE . '/printing-form.php');
  echo ob_get_clean();
}