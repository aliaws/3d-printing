<form id="file-upload-form" class="file-input-container" action="" method="post" enctype="multipart/form-data">
  <input type="file" name="custom_file" id="file_upload_input" accept=".stl">
  <input type="submit" class="hide button wp-element-button" name="upload_button" id="upload_button"
         value="Upload File">
  <input type="hidden" name="product_id" id="stl_product_id" value="<?php echo $product->get_id(); ?>">
  <input type="button" name="ads_add_to_cart" id="ads_add_to_cart" value="Add To Cart"
         class="hide button wp-element-button">
</form>
<div id="stl_estimation" class="stl_estimation"></div>
<div id="add_to_cart_response"></div>