<?php
function register_woocommerce_stl_model_product() {

  class STL_model extends WC_Product {
    public function __construct($product) {
      parent::__construct($product);
    }

    public function get_type(): string {
      return 'stl_model';
    }
  }
}

function add_stl_model_type_selector($types) {
  $types['stl_model'] = __('STL Model');
  return $types;
}


function stl_model_woocommerce_product_class($classname, $product_type) {
  if ($product_type == 'stl_model') {
    $classname = 'STL_model';
  }
  return $classname;
}

add_action("woocommerce_stl_model_add_to_cart", function () {
  do_action('woocommerce_add_to_cart');
});

add_filter('woocommerce_product_class', 'stl_model_woocommerce_product_class', 10, 2);
add_filter('product_type_selector', 'add_stl_model_type_selector');
add_action('init', 'register_woocommerce_stl_model_product');
add_action('woocommerce_product_options_general_product_data', 'stl_model_options_general_product_data');

function stl_model_options_general_product_data() {
  global $product_object;
  if ($product_object && 'stl_model' === $product_object->get_type()) {
    wc_enqueue_js("
         $('.product_data_tabs .general_tab').addClass('show_if_custom').show();
         $('.pricing').addClass('show_if_custom').show();
      ");
  }
}


// Add a shortcode to display the file upload form
function ads_stl_model_printing_estimate_form() {
  global $product;
  ob_start();
  require_once PRINTING_PLUGIN_FRONTEND_BASE . 'file-upload-form.php';
  return ob_get_clean();
}

add_shortcode('ads_stl_model_printing_estimate', 'ads_stl_model_printing_estimate_form');


add_filter('woocommerce_get_price_html', 'hide_stl_model_price', 10, 2);

function hide_stl_model_price($price_html, $product) {
  return $product->get_type() == 'stl_model' ? '' : $price_html;
}


add_action('woocommerce_checkout_create_order_line_item', 'custom_checkout_create_order_line_item', 20, 4);
function custom_checkout_create_order_line_item($item, $cart_item_key, $values, $order) {
  if (!empty($values['stl_price'])) {
    $item->update_meta_data('stl_price', $values['stl_price']);
  }
  if (!empty($values['stl_file'])) {
    $item->update_meta_data('stl_file', $values['stl_file']);
  }
  if (!empty($values['stl_file_url'])) {
    $item->update_meta_data('stl_file_url', $values['stl_file_url']);
  }
  if (!empty($values['volume'])) {
    $item->update_meta_data('volume', $values['volume']);
  }
  if (!empty($values['printing_time'])) {
    $item->update_meta_data('printing_time', $values['printing_time']);
  }
  if (!empty($values['file_name'])) {
    $item->update_meta_data('file_name', $values['file_name']);
  }
}

add_filter('woocommerce_order_item_display_meta_key', 'filter_wc_order_item_display_meta_key', 20, 3);
function filter_wc_order_item_display_meta_key($display_key, $meta, $item) {
  // Change displayed label for specific order item meta key
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'stl_price') {
    $display_key = __("Printing Price", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'printing_time') {
    $display_key = __("Printing Time", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'stl_file') {
    $display_key = __("Uploaded File Path", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'stl_file_url') {
    $display_key = __("Uploaded File URL", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'volume') {
    $display_key = __("Volume", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'file_name') {
    $display_key = __("Original File Name", "woocommerce");
  }
  return ucwords(str_replace('_', ' ', $display_key));
}
