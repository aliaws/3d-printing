jQuery(document).ready(function ($) {
  jQuery('#add_field').on('click', function () {
    const input_field = '<p><input step="0.01" name="layer_heights[]" class="regular-text" type="number" ><span title="Remove field" class="remove_layer_height_btn">x</span></p>';
    jQuery('.nozzle_diameter_wrapper').append(input_field);
  });
  jQuery(document).on('click', '.remove_layer_height_btn', function () {

    jQuery(this).parents("p").remove();
  })

  jQuery('#add_infill_density_list').on('click', function () {
    const input_field = '<p><input step="1" name="infill_density_list[]" class="regular-text" type="number" >' +
      '<button type="button" id="remove_infill_density_list_btn" class="add_button button button-primary">Remove' +
      '</button></p>';
    jQuery('.infill_density_list_wrapper').append(input_field);
  });
  jQuery(document).on('click', '#remove_infill_density_list_btn', function () {
    jQuery(this).parents("p").remove();
  })
});
