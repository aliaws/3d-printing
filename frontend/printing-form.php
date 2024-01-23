<?php
$error_input_classes = 'ads-input-error';
$error_text_classes = 'ads-text-error';

$printing_price = $_POST['printing_price'] ?? get_option('ads_printing_price') ?? null;
$printing_speed = $_POST['printing_speed'] ?? get_option('ads_printing_speed') ?? null;
$nozzle_diameter = $_POST['nozzle_diameter'] ?? get_option('ads_nozzle_diameter') ?? null;
$infill_density = $_POST['infill_density'] ?? get_option('ads_infill_density') ?? false;

$layer_heights = $_POST['layer_heights'] ?? get_option('ads_layer_heights') ? get_option('ads_layer_heights') : [0 => ''];
$infill_density_list = $_POST['infill_density_list'] ?? get_option('ads_infill_density_list') ? get_option('ads_infill_density_list') : [0 => '']; ?>
<form method="post">
  <table class="form-table" role="presentation">
    <tbody>
    <tr>
      <th scope="row">
        <label for="printing_price">Price <small>(per min)</small><span class='ads-text-error'>*</span></label>
      </th>
      <td>
        <input step="0.01" name="printing_price" value="<?php echo $printing_price; ?>" type="number"
               class="<?php echo $data['errors'] && !empty($data['error_messages']['printing_price']) ? $error_input_classes : ""; ?> regular-text"
               id="printing_price">
        <p class="ads-text-error"><?php echo $data['error_messages']['printing_price'] ?? ''; ?></p>
      </td>
    </tr>
    <tr>
      <th scope="row">
        <label for="printing_speed">Printing Speed <small>(mm/s)</small><span class='ads-text-error'>*</span></label>
      </th>
      <td>
        <input name="printing_speed" value="<?php echo $printing_speed; ?>" type="number" id="printing_speed"
               class="<?php echo $data['errors'] && !empty($data['error_messages']['printing_speed']) ? $error_input_classes : ""; ?> regular-text">
        <p class='ads-text-error'><?php echo $data['error_messages']['printing_speed'] ?? ''; ?></p>
      </td>
    </tr>
    <tr>
      <th scope="row">
        <label for="nozzle_diameter">Nozzle Diameter<small>(mm)</small><span class='ads-text-error'>*</span></label>
      </th>
      <td>
        <input step="0.01" name="nozzle_diameter" value="<?php echo $nozzle_diameter; ?>" type="number"
               id="nozzle_diameter"
               class="<?php echo $data['errors'] && !empty($data['error_messages']['nozzle_diameter']) ? $error_input_classes : ""; ?> regular-text">
        <p class="ads-text-error"><?php echo $data['error_messages']['nozzle_diameter'] ?? ''; ?></p>
      </td>
    </tr>
    <tr>
      <th scope="row">
        <label for="nozzle_diameter_0">
          Layer Height <small>(mm)</small>
          <span class='ads-text-error'>*</span>
        </label>
      </th>
      <td>
        <span class="nozzle_diameter_wrapper">
          <?php if (!empty($layer_heights)) {
            foreach ($layer_heights as $key => $value) {
              if ((!empty($_POST['layer_heights']) && (empty($value) || $value == 0)) || !empty($data['duplicate'][$value])) {
                $border_error = 'ads-input-error';
              } else {
                $border_error = '';
              } ?>
              <p>
                <input step="0.01" name="layer_heights[]" class="regular-text <?php echo $border_error ?? ""; ?>"
                       type="number"
                       id="nozzle_diameter_<?php echo $key; ?>" value="<?php echo $value ?? ""; ?>"/>
                <?php if ($key != 0) { ?>
                  <span title="Remove field" class="remove_layer_height_btn">x</span>
                <?php } ?>
              </p>
            <?php }
          } ?>
        </span>
        <p class="ads-text-error"><?php echo $data['error_messages']['layer_heights'] ?? ''; ?></p>
        <button type="button" id="add_field" class="hide add_button button button-primary">+ Add Layer Height</button>
      </td>
    </tr>
    <tr>
      <th scope="row">
        <label for="infill_density">Include Infill Density</label>
      </th>
      <td>
        <span class="infill_density_wrapper">
          <input id="infill_density" name="infill_density"
                 type="checkbox" <?php echo $infill_density ? "checked" : ''; ?>>
        </span>
      </td>
    </tr>
    <tr>
      <th scope="row">
        <label for="infill_density_list">Include Infill Density</label>
      </th>
      <td>
        <span class="infill_density_list_wrapper">
          <?php if (!empty($infill_density_list)) {
            foreach ($infill_density_list as $key => $value) {
              if ((!empty($_POST['layer_heights']) && (empty($value) || $value == 0)) || !empty($data['duplicate'][$value])) {
                $border_error = 'ads-input-error';
              } else {
                $border_error = '';
              } ?>
              <p>
                <input step="1" name="infill_density_list[]" class="regular-text <?php echo $border_error ?? ""; ?>"
                       type="number"
                       id="infill_density_list_<?php echo $key; ?>" value="<?php echo $value ?? ""; ?>"/>
                <?php if ($key != 0) { ?>
                  <button type="button" id="remove_infill_density_list_btn" class="add_button button button-primary">Remove</button>
                <?php } ?>
              </p>
            <?php }
          } ?>
        </span>
        <p class="ads-text-error"><?php echo $data['error_messages']['infill_density_list'] ?? ''; ?></p>
        <button type="button" id="add_infill_density_list" class="add_button button button-primary">
          + Add Infill Density
        </button>
      </td>
    </tr>
    </tbody>
  </table>
  <p class="submit">
    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
  </p>
</form>
