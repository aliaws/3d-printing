<?php
add_filter('elementor/widget/render_content', 'change_heading_widget_content', 10, 2);
function change_heading_widget_content($widget_content, $widget) {
  global $wp_query;
  if ($widget->get_name() == 'nav-menu' && $wp_query->get_queried_object()->post_name == 'support') {
    $widget_content = preg_replace('/<li.*contact.*?<\/li>/', "", $widget_content);
  }
  return $widget_content;
}

add_filter('woocommerce_loop_add_to_cart_link', 'ads_stl_view_product_button', 10, 2);
function ads_stl_view_product_button($button, $product): string {
  $button_text = __("View product", "woocommerce");
  return '<a class="button" href="' . $product->get_permalink() . '">' . $button_text . '</a>';
}