<?php
add_filter('elementor/widget/render_content', 'change_heading_widget_content', 10, 2);
function change_heading_widget_content($widget_content, $widget) {
  global $wp_query;
  if ($widget->get_name() == 'nav-menu' && $wp_query->get_queried_object()->post_name == 'support') {
    $widget_content = preg_replace('/<li.*contact.*?<\/li>/', "", $widget_content);
  }
  return $widget_content;
}
