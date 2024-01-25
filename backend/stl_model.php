<?php


add_action('init', 'register_woocommerce_stl_model_product');
/**
 * This method created a class to register the new woocommerce product STL Model
 * @return void
 */
function register_woocommerce_stl_model_product(): void {

  class STL_model extends WC_Product {
    public function __construct($product) {
      parent::__construct($product);
    }

    /**
     * this method returns the type of current product
     * @return string
     */
    public function get_type(): string {
      return 'stl_model';
    }
  }
}

add_filter('product_type_selector', 'add_stl_model_type_selector');
/**
 * This method adds the newly created custom product to the products type listing
 * @param $types
 * @return mixed
 */
function add_stl_model_type_selector($types): mixed {
  $types['stl_model'] = __('STL Model');
  return $types;
}


add_filter('woocommerce_product_class', 'stl_model_woocommerce_product_class', 10, 2);
/**
 * this method registers the woocommerce class for the new custom product STL Model
 * @param $classname
 * @param $product_type
 * @return mixed|string
 */
function stl_model_woocommerce_product_class($classname, $product_type): mixed {
  if ($product_type == 'stl_model') {
    $classname = 'STL_model';
  }
  return $classname;
}


add_action('woocommerce_product_options_general_product_data', 'stl_model_options_general_product_data');
/**
 * this method shows the general product tab to add the price for the custom product STL Model
 * @return void
 */
function stl_model_options_general_product_data(): void {
  global $product_object;
  if ($product_object && 'stl_model' === $product_object->get_type()) {
    wc_enqueue_js("
         $('.product_data_tabs .general_tab').addClass('show_if_custom').show();
         $('.pricing').addClass('show_if_custom').show();
      ");
  }
}


add_shortcode('ads_stl_printing_form', 'ads_stl_model_printing_estimate_form');
/**
 * this method render the form to upload the STL file to make calculations
 * @return false|string
 */
function ads_stl_model_printing_estimate_form(): bool|string {
  global $product;
  $infill_density_values = [];
  if (get_option('ads_infill_density')) {
    $infill_density_values = get_option('ads_infill_density_values');
  }
  $default_set = get_option('ads_default_infill_density') ?? 0;
  ob_start();
  require_once STL_PLUGIN_DIR . '/frontend/file-upload-form.php';
  return ob_get_clean();
}


add_filter('woocommerce_get_price_html', 'hide_stl_model_price', 10, 2);
/**
 * This method hides the stl model product type price and displays the file upload form
 * for the users to choose the STL file to upload and get pricing and time estimates
 * @param $price_html
 * @param $product
 * @return false|mixed|string
 */
function hide_stl_model_price($price_html, $product): mixed {
  if (is_shop()) {
    return $product->get_type() == 'stl_model' ? '' : $price_html;
  } else {
    return $product->get_type() == 'stl_model' ? ads_stl_model_printing_estimate_form() : $price_html;
  }
}


add_action('woocommerce_checkout_create_order_line_item', 'custom_checkout_create_order_line_item', 20, 4);
/**
 * This method sets the metadata of the cart stl_model type products
 * @param $item
 * @param $cart_item_key
 * @param $values
 * @param $order
 * @return void
 */
function custom_checkout_create_order_line_item($item, $cart_item_key, $values, $order): void {
  if (!empty($values['stl_price'])) {
    $item->update_meta_data('stl_price', $values['stl_price']);
  }
  if (!empty($values['stl_file'])) {
    $item->update_meta_data('stl_file', $values['stl_file']);
  }

  if (!empty($values['volume'])) {
    $item->update_meta_data('volume', $values['volume']);
  }
  if (!empty($values['printing_time'])) {
    $item->update_meta_data('_printing_time', $values['printing_time']);
  }
  if (!empty($values['file_name'])) {
    $item->update_meta_data('file_name', $values['file_name']);
  }
  if (!empty($values['infill_density'])) {
    $item->update_meta_data('_infill_density', $values['infill_density']);
  }
  if (!empty($values['infill_density_label'])) {
    $item->update_meta_data('infill_density_label', $values['infill_density_label']);
  }
}


add_filter('woocommerce_order_item_display_meta_key', 'filter_wc_order_item_display_meta_key', 20, 3);
/**
 * This method sets the label of the custom keys added in the cart and order object
 * @param $display_key
 * @param $meta
 * @param $item
 * @return string
 */
function filter_wc_order_item_display_meta_key($display_key, $meta, $item): string {
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'stl_price') {
    $display_key = __("Printing Price", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === '_printing_time') {
    $display_key = __("Printing Time", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'stl_file') {
    $display_key = __("Uploaded STL File", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'volume') {
    $display_key = __("Volume", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'file_name') {
    $display_key = __("Original File Name", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === '_infill_density') {
    $display_key = __("Infill Density", "woocommerce");
  }
  if (is_admin() && $item->get_type() === 'line_item' && $meta->key === 'infill_density_label') {
    $display_key = __("Infill Density Label", "woocommerce");
  }
  return ucwords(str_replace('_', ' ', $display_key));
}


add_filter('mime_types', 'edit_upload_types');
/**
 * this method registers the mime type in the system to support STL files upload
 * @param array $existing_mimes
 * @return array
 */
function edit_upload_types(array $existing_mimes = []): array {
  $existing_mimes['stl'] = 'application/wavefront-stl';
  return $existing_mimes;
}

//add_filter('woocommerce_order_item_display_meta_value', 'change_order_item_meta_value', 20, 3);
//function change_order_item_meta_value($value, $meta, $item) {
//  if ($meta->key == 'infill_density') {
//    $value .= '%';
//  }
//  return $value;
//}