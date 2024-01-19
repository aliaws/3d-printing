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


add_action("wp_ajax_stl_add_to_cart_handler", "stl_add_to_cart_handler");
add_action("wp_ajax_nopriv_stl_add_to_cart_handler", "stl_add_to_cart_handler");
/**
 * This method adds the current STL model file to cart
 * @throws Exception
 */
function stl_add_to_cart_handler() {

  $cart_item_data = [
    'stl_price' => $_POST['price'],
    'stl_file' => $_POST['file_url'],
    'volume' => $_POST['volume'],
    'printing_time' => $_POST['printing_time'],
    'file_name' => $_POST['file_name']
  ];

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
    require_once(STL_PLUGIN_DIR . '/backend/stl_calculator.php');
    $stl_calculator = new STLCalc($upload['file']);
    $volume = $stl_calculator->GetVolume('cm');
    [$time_in_seconds, $formatted_time] = $stl_calculator->CalculatePrintingTime($volume);
    $printing_price = $stl_calculator->CalculatePrintingPrice($time_in_seconds);
    echo calculated_price_volume_response($volume, $time_in_seconds, $formatted_time, $printing_price, $upload, $_FILES['file']['name']);
  } else {
    echo file_upload_error($upload['error']);
  }
  wp_die();
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
 * @param $upload
 * @param $original_file_name
 * @return bool|string
 */
function calculated_price_volume_response($volume, $time_in_seconds, $formatted_time, $printing_price, $upload, $original_file_name): bool|string {
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
  $product_name .= !empty($cart_item['printing_time']) ? "<br> * Printing Time: {$cart_item['printing_time']}" : "";
  return $product_name;
}

