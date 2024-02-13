<?php

add_action('woocommerce_before_calculate_totals', 'stl_custom_price_update', 1000, 1);
/**
 * the method updates the stl model type product while being added to cart
 * @param $cart
 * @return void
 */
function stl_custom_price_update($cart): void {
  if (is_admin() && !defined('DOING_AJAX')) {
    return;
  }

  if (did_action('woocommerce_before_calculate_totals') >= 2) {
    return;
  }

  foreach ($cart->get_cart() as $cart_item) {
    if (!empty($cart_item['stl_price'])) {
      $cart_item['data']->set_price($cart_item['stl_price']);
    }
  }
}


add_action("wp_ajax_ads_stl_add_to_cart_handler", "ads_stl_add_to_cart_handler");
add_action("wp_ajax_nopriv_ads_stl_add_to_cart_handler", "ads_stl_add_to_cart_handler");
/**
 * This method adds the current STL model file to cart
 * @throws Exception
 */
function ads_stl_add_to_cart_handler() {

  $cart_item_data = [
    'stl_price' => $_POST['price'],
    'stl_file' => $_POST['file_url'],
    'volume' => $_POST['volume'],
    'printing_time' => $_POST['printing_time'],
    'file_name' => $_POST['file_name'],
    'infill_density' => $_POST['infill_density'],
    'unit' => $_POST['unit'],
    'layer_height' => $_POST['layer_height'],
//    'infill_density_label' => $_POST['infill_density_label']
  ];
  if ($_POST['infill_density_label'] != '') {
    $cart_item_data['infill_density_label'] = $_POST['infill_density_label'];
  }

  $cart_item_key = WC()->cart->add_to_cart(product_id: $_POST['product_id'], cart_item_data: $cart_item_data);
  if ($cart_item_key) {
    echo render_add_to_cart_response($_POST['file_name']);
  }
  wp_die();
}

/**
 * this method renders the response after the cart upload
 * @param $file_name
 * @return false|string
 */
function render_add_to_cart_response($file_name): bool|string {
  ob_start();
  require_once STL_PLUGIN_DIR . '/frontend/add_to_cart_response.php';
  return ob_get_clean();
}


add_action("wp_ajax_ads_stl_change_in_density_handler", "ads_stl_change_in_density_handler");
add_action("wp_ajax_nopriv_ads_stl_change_in_density_handler", "ads_stl_change_in_density_handler");
/**
 * this method prepares the estimation response after the client changes the infill density parameter
 * @return void
 */
function ads_stl_change_in_density_handler() {
  $file_path = str_replace(get_site_url() . "/wp-content/uploads", wp_upload_dir()['basedir'], $_POST['file_url']);
  echo prepare_stl_estimation_response($file_path, $_POST['file_name'], $_POST['infill_density'], $_POST['infill_density_label'], $_POST['file_url'], $_POST['layer_height']);
  wp_die();
}

add_action("wp_ajax_ads_stl_form_submission_handler", "ads_stl_form_submission_handler");
add_action("wp_ajax_nopriv_ads_stl_form_submission_handler", "ads_stl_form_submission_handler");
/**
 * this method handles the STL file upload and calculates the estimated price and time to print
 * @return void
 */
function ads_stl_form_submission_handler() {
  if (!function_exists('wp_handle_upload')) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
  }
  $upload = wp_handle_upload($_FILES['file'], array('test_form' => false, 'unique_filename_callback' => null));
  if ($upload && !isset($upload['error'])) {
    echo prepare_stl_estimation_response($upload['file'], $_FILES['file']['name'], $_POST['infill_density'], $_POST['infill_density_label'], $upload['url'], $_POST['layer_height']);
  } else {
    echo file_upload_error($upload['error']);
  }
  wp_die();
}


/**
 * this method prepares the response of STL estimations
 * @param $file_path
 * @param $file_name
 * @param $infill_density
 * @param $infill_density_label
 * @param $uploaded_file_url
 * @param $layer_height
* @param $unit
 * @return bool|string
 */
function prepare_stl_estimation_response($file_path, $file_name, $infill_density, $infill_density_label, $uploaded_file_url, $layer_height, $unit = 'mm'): bool|string {
  require_once(STL_PLUGIN_DIR . '/backend/stl_calculator.php');
  $stl_calculator = new STLCalc($file_path);
  
  $volume = $stl_calculator->getVolume($unit);
  $hard_limit = (int) get_option("ads_hard_limit");
  $height = 100;
  if($hard_limit > 0 && $height > $hard_limit){
    // throw error
  }

  [$time_in_seconds, $formatted_time] = $stl_calculator->calculatePrintingTime($volume, $infill_density, $layer_height);
  $printing_price = $stl_calculator->calculatePrintingPrice($time_in_seconds);
  return calculated_price_volume_response($volume, $time_in_seconds, $formatted_time, $printing_price, $uploaded_file_url, $file_name, $infill_density, $infill_density_label);
}

/**
 * this method renders the error information if any occurred during the file upload or calculations
 * @param $error
 * @return bool|string
 */
function file_upload_error($error): bool|string {
  ob_start();
  require_once STL_PLUGIN_DIR . '/frontend/upload_error.php';
  return ob_get_clean();
}

/**
 * This method renders the estimated volume and pricing information after the file upload
 * @param $volume
 * @param $time_in_seconds
 * @param $formatted_time
 * @param $printing_price
 * @param $uploaded_file_url
 * @param $original_file_name
 * @param $infill_density
 * @param $infill_density_label
 * @return bool|string
 */
function calculated_price_volume_response($volume, $time_in_seconds, $formatted_time, $printing_price, $uploaded_file_url, $original_file_name, $infill_density, $infill_density_label): bool|string {
  ob_start();
  require_once STL_PLUGIN_DIR . '/frontend/stl_estimation.php';
  return ob_get_clean();
}


add_filter('woocommerce_cart_item_name', 'ads_update_cart_line_items', 10, 3);
/**
 * this method updates the cart and checkout summary to show the uploaded STL file(s) information with the product name
 * @param $product_name
 * @param $cart_item
 * @param $cart_item_key
 * @return string
 */
function ads_update_cart_line_items($product_name, $cart_item, $cart_item_key): string {
  $product_name .= !empty($cart_item['file_name']) ? "<br> * File: {$cart_item['file_name']}" : "";
  $product_name .= !empty($cart_item['volume']) ? "<br> * Model Volume: {$cart_item['volume']}" : "";
  $product_name .= !empty($cart_item['infill_density_label']) ? "<br> * Infill Density: {$cart_item['infill_density_label']}" : "";
  $product_name .= !empty($cart_item['layer_height']) ? "<br> * Layer height: {$cart_item['layer_height']} mm" : "";
  $product_name .= !empty($cart_item['unit']) ? "<br> * Unit: {$cart_item['unit']}" : "";

  if (is_admin()) {
    $product_name .= !empty($cart_item['printing_time']) ? "<br> * Printing Time: {$cart_item['printing_time']}" : "";
  }
  return $product_name;
}

