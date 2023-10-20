<?php
echo "<pre>";
print_r($data);
echo "</pre>";
$error_input_classes = 'ads-input-error';
$error_text_classes = 'ads-text-error';
$printing_price = get_option('ads_printing_price');
$printing_speed = get_option('ads_printing_speed');
$nozzle_diameter = get_option('ads_nozzle_diameter');
if (!$layer_heights = get_option('ads_layer_heights')) {
  $layer_heights = [0 => ""];
}
$printing_price = empty($printing_price) || !empty($_POST) ? $_POST['printing_price'] : $printing_price;
$printing_speed = empty($printing_speed) || !empty($_POST) ? $_POST['printing_speed'] : $printing_speed;
$nozzle_diameter = empty($nozzle_diameter) || !empty($_POST) ? $_POST['nozzle_diameter'] : $nozzle_diameter;
$layer_heights = !empty($_POST['layer_heights']) ? $_POST['layer_heights'] : $layer_heights;

?>
<style>
    .ads-input-error {
        border-color: #c61010 !important;
    }

    .ads-text-error {
        color: #c61010 !important;
    }

    .remove_layer_height_btn,
    .add_button {
        font-size: 18px !important;
        margin-left: 10px;
        vertical-align: middle;
        cursor: pointer;
    }

    .add_button {
        font-size: 13px !important;
        margin-top: 10px !important;
    }

    .remove_layer_height_btn:hover,
    .add_button:hover {
        color: #494949;
    }
</style>
<form method="post">
  <table class="form-table" role="presentation">
    <tbody>
    <tr>
      <th scope="row">
        <label for="printing_price">
          Price
          <small>(per mm)</small>
          <span class='ads-text-error'>*</span>
        </label>
      </th>
      <td>
        <!--                <input type="number" id="floatInput" step="0.01" placeholder="0.00"/>-->
        <input step="0.01" name="printing_price"
               value="<?php echo $printing_price; ?>"
               type="number" id="printing_price"
               class="<?php echo $data['errors'] && !empty($data['error_messages']['printing_price']) ? $error_input_classes : ""; ?> regular-text">
        <p class="ads-text-error"><?php echo $data['error_messages']['printing_price'] ?? ''; ?></p>

      </td>
    </tr>
    <tr>
      <th scope="row">
        <label for="printing_speed">Printing Speed <span class='ads-text-error'>*</span></label>
      </th>
      <td>
        <input name="printing_speed" value="<?php echo $printing_speed; ?>"
               type="number" id="printing_speed"
               class="<?php echo $data['errors'] && !empty($data['error_messages']['printing_speed']) ? $error_input_classes : ""; ?> regular-text">
        <p class='ads-text-error'><?php echo $data['error_messages']['printing_speed'] ?? ''; ?></p>
      </td>
    </tr>
    <tr>
      <th scope="row">
        <label for="nozzle_diameter">Nozzle Diameter/Size <span class='ads-text-error'>*</span></label>
      </th>
      <td>
        <input name="nozzle_diameter" value="<?php echo $nozzle_diameter; ?>"
               type="number" id="nozzle_diameter"
               class="<?php echo $data['errors'] && !empty($data['error_messages']['nozzle_diameter']) ? $error_input_classes : ""; ?> regular-text">
        <p class="ads-text-error"><?php echo $data['error_messages']['nozzle_diameter'] ?? ''; ?></p>
      </td>
    </tr>
    <tr>
      <th scope="row">
        <label for="nozzle_diameter">Layer Heights <span class='ads-text-error'>*</span></label>
      </th>
      <td>
        <span class="nozzle_diameter_wrapper">

          <?php
          if (!empty($layer_heights)) {
            foreach ($layer_heights as $key => $value) {
              ?>
              <?php

              if ((!empty($_POST['layer_heights']) && (empty($value) || $value == 0)) || !empty($data['duplicate'][$value])) {
                $border_error = 'ads-input-error';
              } else {
                $border_error = '';
              }
              ?>
              <p>
                <input name="layer_heights[]" class="regular-text <?php echo $border_error ?? ""; ?>" type="number"
                       id="nozzle_diameter_<?php echo $key; ?>" value="<?php echo $value ?? ""; ?>"/>
                <?php
                if ($key != 0) {
                  ?>
                  <span title="Remove field" class="remove_layer_height_btn">x</span>
                  <?php
                }
                ?>
              </p>
              <?php
            }
          }
          ?>
        </span>
        <p class="ads-text-error"><?php echo $data['error_messages']['layer_heights'] ?? ''; ?></p>

        <button type="button" id="add_field" class="add_button button button-primary">+ Add Layer Height</button>
      </td>
    </tr>
    </tbody>
  </table>
  <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
  </p>
</form>
<script>
    jQuery('#add_field').on('click', function () {
        const input_field = '<p><input name="layer_heights[]" class="regular-text" type="number" ><span title="Remove field" class="remove_layer_height_btn">x</span></p>';
        jQuery('.nozzle_diameter_wrapper').append(input_field);
    });
    jQuery(document).on('click', '.remove_layer_height_btn', function () {

        jQuery(this).parents("p").remove();
    })
</script>