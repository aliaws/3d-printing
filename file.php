<?php
include_once(PRINTING_PLUGIN_BACKEND_BASE . '/index.php');
include_once(PRINTING_PLUGIN_FRONTEND_BASE . '/index.php');
include_once(PRINTING_PLUGIN_ADMIN_BASE . '/index.php');

function rental_scripts() {
  wp_enqueue_style('custom_style', PRINTING_ASSETS_URL . 'css/custom.css', false, '1.2');
  wp_enqueue_script('custom_js', PRINTING_ASSETS_URL . 'js/custom.js', false, '1.2', true);
}

add_action('admin_enqueue_scripts', 'rental_scripts');
add_action('wp_enqueue_scripts', 'my_admin_scripts');