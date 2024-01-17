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

//
add_shortcode('ads_stl_model_printing_estimate', 'ads_stl_model_printing_estimate_form');