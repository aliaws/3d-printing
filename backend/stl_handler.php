<?php


add_action('woocommerce_before_calculate_totals', 'stl_custom_price_update', 1000, 1);
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
 * @throws Exception
 */
function stl_add_to_cart_handler() {

  $cart_item_data = [
    'stl_price' => $_POST['price'],
    'stl_file' => $_POST['file_path'],
    'volume' => $_POST['volume'],
    'printing_time' => $_POST['printing_time'],
    'file_name' => $_POST['file_name']
  ];
  $cart_item_key = WC()->cart->add_to_cart(product_id: $_POST['product_id'], cart_item_data: $cart_item_data);
  if ($cart_item_key) {
    echo send_add_to_cart_response($_POST['file_name']);
  }
  wp_die();
}

function send_add_to_cart_response($file_name) {
  ob_start();
  require_once PRINTING_PLUGIN_FRONTEND_BASE . 'add_to_cart_response.php';
  return ob_get_clean();
}


add_action("wp_ajax_ads_stl_form_submission_handler", "ads_stl_form_submission_handler");
add_action("wp_ajax_nopriv_ads_stl_form_submission_handler", "ads_stl_form_submission_handler");

function ads_stl_form_submission_handler() {
  if (!function_exists('wp_handle_upload')) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
  }

  $upload = wp_handle_upload($_FILES['file'], array('test_form' => false, 'unique_filename_callback' => null));
  if ($upload && !isset($upload['error'])) {
    require_once(PRINTING_PLUGIN_BACKEND_BASE . '/stl_calculator.php');
    $stl_calculator = new STLCalc($upload['file']);
    $volume = $stl_calculator->GetVolume('cm');
    [$time_in_seconds, $formatted_time] = $stl_calculator->CalculatePrintingTime($volume);
    $printing_price = $stl_calculator->CalculatePrintingPrice($time_in_seconds);
    echo calculated_price_volume_response($volume, $time_in_seconds, $formatted_time, $printing_price, $upload['file'], $_FILES['file']['name']);
  } else {
    echo file_upload_error($upload['error']);
  }
  wp_die();
}

function file_upload_error($error) {
  ob_start();
  require_once PRINTING_PLUGIN_FRONTEND_BASE . 'upload_error.php';
  return ob_get_clean();
}


function calculated_price_volume_response($volume, $time_in_seconds, $formatted_time, $printing_price, $file_path, $original_file_name) {
  ob_start();
  require_once PRINTING_PLUGIN_FRONTEND_BASE . 'stl_estimation.php';
  return ob_get_clean();
}


add_filter('woocommerce_cart_item_name', 'ads_update_cart_line_items', 10, 3);
function ads_update_cart_line_items($product_name, $cart_item, $cart_item_key) {
  $product_name .= !empty($cart_item['file_name']) ? "<br> * File: {$cart_item['file_name']}" : "";
  $product_name .= !empty($cart_item['volume']) ? "<br> * Model Volume: {$cart_item['volume']}" : "";
  $product_name .= !empty($cart_item['printing_time']) ? "<br> * Printing Time: {$cart_item['printing_time']}" : "";
  return $product_name;
}