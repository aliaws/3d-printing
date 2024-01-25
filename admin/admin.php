<?php
add_action('admin_menu', 'register_my_custom_menu_page');
/**
 * @return void
 * this hock is used for the add plugin setting page on the admin sidebar
 */
function register_my_custom_menu_page(): void {
//  add_menu_page('3D Printing', '3D Printing', 'stl_plugin_manager', 'my-plugin-settings', 'save_printer_properties');
  add_menu_page('3D Printing', '3D Printing', 'manage_options', 'printer-properties', 'save_printer_properties');
}

/**
 * This method renders the printer attributes in the wp_options table
 * @return void
 */
function save_printer_properties(): void {
  $data = validate_printer_properties_form();
  $error_input_classes = 'ads-input-error';
  $error_text_classes = 'ads-text-error';

  $default_printing_price = $_POST['default_printing_price'] ?? get_option('ads_default_printing_price') ?? null;
  $printing_price = $_POST['printing_price'] ?? get_option('ads_printing_price') ?? null;
  $printing_speed = $_POST['printing_speed'] ?? get_option('ads_printing_speed') ?? null;
  $nozzle_diameter = $_POST['nozzle_diameter'] ?? get_option('ads_nozzle_diameter') ?? null;
  $infill_density = $_POST['infill_density'] ?? get_option('ads_infill_density') ?? false;
  $default_infill_density = $_POST['default_infill_density'] ?? get_option('ads_default_infill_density') ?? 0;

  $layer_heights = $_POST['layer_heights'] ?? get_option('ads_layer_heights') ? get_option('ads_layer_heights') : [0 => ''];
  $infill_density_values = $_POST['infill_density_values'] ?? get_option('ads_infill_density_values') ? get_option('ads_infill_density_values') : [0 => ''];

  if (array_key_exists('infill_density_values', $_POST)) {
    $infill_density_values = array_combine($_POST['infill_density_values'], $_POST['infill_density_labels']);
  } else {
    $infill_density_values = get_option('ads_infill_density_values') ? get_option('ads_infill_density_values') : [0 => ''];
  }

  ob_start();
  include_once(STL_PLUGIN_DIR . '/frontend/printing-form.php');
  echo ob_get_clean();
}

/**
 * This method validates the inputs of the printing form attributes and saves in wp_options table
 * @return array
 */
function validate_printer_properties_form(): array {
  $errors = true;
  $error_messages = [];
  if (empty($_POST)) {
    $errors = false;
  } else {
    $error_messages = validate_input_properties();
    $duplicate = duplicate_array();
    $_POST['infill_density'] = !empty($_POST['infill_density']);
    $infill_density_values = array_combine($_POST['infill_density_values'], $_POST['infill_density_labels']);
    if (empty($error_messages)) {
      update_option('ads_printing_price', $_POST['printing_price']);
      update_option('ads_default_printing_price', $_POST['default_printing_price']);
      update_option('ads_printing_speed', $_POST['printing_speed']);
      update_option('ads_nozzle_diameter', $_POST['nozzle_diameter']);
      update_option('ads_layer_heights', $_POST['layer_heights']);
      update_option('ads_infill_density', $_POST['infill_density']);
      update_option('ads_infill_density_values', $infill_density_values);
      update_option('ads_default_infill_density', $_POST['default_infill_density'] ?? 0);
    }
  }
  return [
    'duplicate' => $duplicate ?? '',
    'errors' => $errors,
    'error_messages' => $error_messages
  ];

}


/**
 * This method renders the array of errors while adding the admin fields
 * @return array
 */
function validate_input_properties(): array {
  $error_messages = [];
  if (empty($_POST['printing_speed']) || $_POST['printing_speed'] == 0) {
    $error_messages['printing_speed'] = 'Printing Speed must not be empty or 0';
  }
  if (empty($_POST['printing_price']) || $_POST['printing_price'] == 0) {
    $error_messages['printing_price'] = 'Printing Price must not be empty or 0';
  }
  if (empty($_POST['default_printing_price']) || $_POST['default_printing_price'] == 0) {
    $error_messages['default_printing_price'] = 'Default Printing Price must not be empty or 0';
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
    $error_messages['layer_heights'] = 'Layer Height must not be Duplicate, empty, or 0';
  }
  if (count($_POST['infill_density_values']) != count(array_unique($_POST['infill_density_values']))) {
    $error_messages['infill_density_values'] = 'Infill Densities List must not be Duplicate, empty, or 0';
  }
  if (count($_POST['infill_density_values']) != count($_POST['infill_density_labels'])) {
    $error_messages['infill_density_values'] .= '<br>Please provide all labels and values';
  }
  return $error_messages;
}


/**
 * this method checks for the duplicate array
 * @return array
 */
function duplicate_array(): array {
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


//function stl_register_manager_role(): void {
//  $role = get_role('shop_manager');
//  $role->add_cap('stl_plugin_manager');
//  $role = get_role('administrator');
//  $role->add_cap('stl_plugin_manager');
//}
//
//// Add the simple_role.
//add_action('init', 'stl_register_manager_role');

