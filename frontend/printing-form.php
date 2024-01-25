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
                  <span title="Remove field" class="remove-layer-height-btn">x</span>
                <?php } ?>
              </p>
            <?php }
          } ?>
        </span>
        <p class="ads-text-error"><?php echo $data['error_messages']['layer_heights'] ?? ''; ?></p>
        <button type="button" id="add_field" class="hide add-button button button-primary">+ Add Layer Height</button>
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
        <label for="infill_density_values">Infill Labels and Multipliers</label>
      </th>
      <td>
        <span class="infill-density-values-wrapper">
          <p>
            <label for="infill_density_labels_0">Label</label>
            <label for="infill_density_values_0">Value</label>
            <label for="infill_density_default_0">Default Set</label>
          </p>
          <?php if (!empty($infill_density_values)) {
            $itr = 0;
            foreach ($infill_density_values as $key => $value) {
              if ((!empty($_POST['layer_heights']) && (empty($value) || $value == 0)) || !empty($data['duplicate'][$value])) {
                $border_error = 'ads-input-error';
              } else {
                $border_error = '';
              } ?>
              <p>
                <input name="infill_density_labels[]" class="regular-text <?php echo $border_error ?? ""; ?>"
                       type="text" placeholder="Label"
                       id="infill_density_labels_<?php echo $itr; ?>" value="<?php echo $value ?? ""; ?>"/>
                <input step="0.01" name="infill_density_values[]" type="number" placeholder="Value"
                       class="regular-text <?php echo $border_error ?? ""; ?>"
                       id="infill_density_values_<?php echo $itr; ?>" value="<?php echo $key ?? ""; ?>"/>
                <input type="radio" id="infill_density_default_<?php echo $itr; ?>" name="default_infill_density"
                       value="<?php echo $itr; ?>" <?php echo $itr == $default_infill_density ? 'checked' : ''; ?>>
                <?php if ($itr != 0) { ?>
                  <button type="button" id="remove_infill_density_values_btn"
                          class="button button-primary">Remove</button>
                <?php } ?>
              </p>
              <?php $itr++;
            }
          } ?>
        </span>
        <p class="ads-text-error"><?php echo $data['error_messages']['infill_density_values'] ?? ''; ?></p>
        <button type="button" id="add_infill_density_values" class="add-button button button-primary">
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
