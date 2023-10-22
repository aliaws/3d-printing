<?php
add_action('admin_menu', 'register_my_custom_menu_page');
/**
 * @return void
 * this hock is used for the add plugin setting page on the admin sidebar
 */
function register_my_custom_menu_page() {
  add_menu_page('3D Printing', '3D Printing', 'manage_options', 'my-plugin-settings', 'save_printer_properties');
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
    'duplicate' => $duplicate ?? '',
    'errors' => $errors,
    'error_messages' => $error_messages
  ];

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

