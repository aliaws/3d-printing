<p>Please Choose a STL file to upload.</p>
<form id="file-upload-form" class="file-input-container" action="" method="post" enctype="multipart/form-data">
  <div>
    <input type="file" class="full-width" name="custom_file" id="file_upload_input" accept=".stl" disabled>
    <progress id="progressBar" class="stl-progress-bar full-width" value="0" max="100"></progress>
    <?php if (!empty($infill_density_values)) { ?>
      <label for="infill_density">Infill Density:</label>
      <select name="infill_density" id="infill_density">
        <?php foreach ($infill_density_values as $key => $value) { ?>
          <option value="<?php echo $key ?>"><?php echo $value; ?></option>
        <?php } ?>
      </select>
    <?php } ?>
  </div>
  <div id="file_upload_loader" class="load hide"></div>
  <input type="submit" class="hide button wp-element-button" name="upload_button" id="upload_button"
         value="Upload File">
  <input type="hidden" name="product_id" id="stl_product_id" value="<?php echo $product->get_id(); ?>">
  <input type="button" name="ads_add_to_cart" id="ads_add_to_cart" value="Add To Cart"
         class="hide button wp-element-button">
  <a id="ads_view_cart" class="hide button wp-element-button" href="<?php echo wc_get_cart_url(); ?>" target="_self">View
    Cart</a>
</form>
<div id="stl_estimation" class="stl_estimation"></div>
<div id="add_to_cart_response"></div>
<script>
  jQuery('form#file-upload-form').parent().removeClass('has-large-font-size')
</script>