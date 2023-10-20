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

const PRINTING__FILE__ = __FILE__;
define('PRINTING_PLUGIN_PATH', plugin_dir_path(PRINTING__FILE__));

include PRINTING_PLUGIN_PATH . 'file.php';

add_action('admin_menu', 'register_my_custom_menu_page');

function register_my_custom_menu_page() {
  add_menu_page('3D Printing', '3D Printing', 'manage_options', 'my-plugin-settings', 'save_printer_properties');
}


function save_printer_properties() {
  $data = validate_printer_properties_form();
  ob_start();
  include PRINTING_PLUGIN_PATH . 'printing-form.php';
  $some_var = ob_get_clean();
  echo $some_var;
}

function validate_printer_properties_form() {
  $errors = true;
  $error_messages = [];
  if (empty($_POST)) {
    $errors = false;
  } else {
    $error_messages = validate_input_properties();
    $duplicate = duplicate_array();
    if (empty($error_messages)) {
      update_option('ads_printing_price', $_POST['printing_price']);
      update_option('ads_printing_speed', $_POST['printing_speed']);
      update_option('ads_nozzle_diameter', $_POST['nozzle_diameter']);
      update_option('ads_layer_heights', $_POST['layer_heights']);
    }
  }
  return [
    'duplicate' => $duplicate,
    'errors' => $errors,
    'error_messages' => $error_messages
  ];

}

function duplicate_array() {
  $duplicate = [];
  $uni = [];
  foreach ($_POST['layer_heights'] as $value) {
    if (isset($uni[$value])) {
      $duplicate[$value] = $value;
    } else {
      $uni[$value] = $value;
    }
  }
  return $duplicate;
}

function validate_input_properties() {
  $error_messages = [];
  if (empty($_POST['printing_speed']) || $_POST['printing_speed'] == 0) {
    $error_messages['printing_speed'] = 'Printing Speed must not be empty or 0';
  }
  if (empty($_POST['printing_price']) || $_POST['printing_price'] == 0) {
    $error_messages['printing_price'] = 'Printing Price must not be empty or 0';
  }
  if (empty($_POST['nozzle_diameter']) || $_POST['nozzle_diameter'] == 0) {
    $error_messages['nozzle_diameter'] = 'Nozzle Diameter must not be empty or 0';
  }
  foreach ($_POST['layer_heights'] as $value) {
    if (empty($value) || $value == 0) {
      $error_messages['layer_heights'] = 'Layer Height must not be empty or 0';
    }
  }
  if (count($_POST['layer_heights']) != count(array_unique($_POST['layer_heights']))) {
    $error_messages['layer_heights'] = 'Layer Height must not be Duplicate and empty or 0';
  }
  return $error_messages;
}

;

?>
