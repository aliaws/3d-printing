<?php
/**
 * Plugin Name: 3D Printing
 * Plugin URI: https://not.yet/
 * Description:
 * Version: 1.1
 * Author: ADS
 * Author URI: https://office.com/
 * License: GPLv2 or later
 * Text Domain: ADS
 */
const PRINTING_ROOT_DIR = __DIR__;

require_once(PRINTING_ROOT_DIR . '/const.php');
include PRINTING_PLUGIN_PATH . 'file.php';

function save_printer_properties() {
  $data = validate_printer_properties_form();
  ob_start();
  include_once(PRINTING_PLUGIN_FRONTEND_BASE . '/printing-form.php');
  $some_var = ob_get_clean();
  echo $some_var;
}

?>
