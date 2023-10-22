jQuery('#add_field').on('click', function () {
    const input_field = '<p><input name="layer_heights[]" class="regular-text" type="number" ><span title="Remove field" class="remove_layer_height_btn">x</span></p>';
    jQuery('.nozzle_diameter_wrapper').append(input_field);
});
jQuery(document).on('click', '.remove_layer_height_btn', function () {

    jQuery(this).parents("p").remove();
})