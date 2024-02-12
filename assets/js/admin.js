jQuery(document).ready(function ($) {
  jQuery('#add_field').on('click', function () {
    const input_field = `<p><input step="0.01" name="layer_heights[]" class="regular-text" type="number" >` +
      `<span title="Remove field" class="remove-layer-height-btn">x</span></p>`;
    jQuery('.nozzle_diameter_wrapper').append(input_field);
  });
  jQuery(document).on('click', '.remove-layer-height-btn', function () {

    jQuery(this).parents("p").remove();
  })

  jQuery('#add_infill_density_values').on('click', function () {
    let itr = jQuery('.infill-density-values-wrapper p').length - 1;
    const input_field = `<p>` +
      `<input placeholder="Label" name="infill_density_labels[]" class="regular-text" type="text" >` +
      `<input step="0.01" placeholder="Value" name="infill_density_values[]" class="regular-text" type="number" >` +
      `<input type="radio" id="infill_density_default_${itr}" name="default_infill_density" value="${itr}" >` +
      `<button type="button" id="remove_infill_density_values_btn" class="button button-primary">Remove` +
      `</button></p>`;
    jQuery('.infill-density-values-wrapper').append(input_field);
  });
  jQuery(document).on('click', '#remove_infill_density_values_btn', function () {
    jQuery(this).parents("p").remove();
  })
});
