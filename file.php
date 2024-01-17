<?php
include_once(PRINTING_PLUGIN_BACKEND_BASE . '/index.php');
include_once(PRINTING_PLUGIN_BACKEND_BASE . '/stl_handler.php');
include_once(PRINTING_PLUGIN_ADMIN_BASE . '/index.php');

function ads_stl_admin_scripts(): void {
  wp_enqueue_style('ads-stl-style', PRINTING_ASSETS_URL . 'css/custom.css', false, '1.2');
  wp_enqueue_script('ads-stl-js', PRINTING_ASSETS_URL . 'js/admin.js', false, '1.3', true);
}

function ads_stl_client_scripts(): void {
  wp_enqueue_style('ads-upload-stl-style', PRINTING_ASSETS_URL . 'css/upload-form.css', false, '1.2');
  wp_enqueue_script('ads-upload-stl-js', PRINTING_ASSETS_URL . 'js/upload-form.js', false, '1.3', true);
  wp_localize_script('ads-upload-stl-js', 'frontend_ajax', array('ajaxURL' => admin_url('admin-ajax.php')));
}

add_action('admin_enqueue_scripts', 'ads_stl_admin_scripts');
add_action('wp_enqueue_scripts', 'ads_stl_client_scripts');
add_filter('mime_types', 'edit_upload_types');


function edit_upload_types($existing_mimes = array()) {
  $existing_mimes['stl'] = 'application/wavefront-stl';
  return $existing_mimes;
}

