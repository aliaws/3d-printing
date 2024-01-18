<table>
  <tr>
    <td>Model Volume:</td>
    <td><strong><?php echo $volume; ?></strong></td>
  </tr>
  <tr>
    <td>Estimated Printing Time:</td>
    <td><strong><?php echo $formatted_time; ?></strong></td>
  </tr>
  <tr>
    <td>Estimated Printing Price:</td>
    <td><strong><?php echo $printing_price; ?></strong></td>
  </tr>
</table>

<div class="hide">
  <input type="hidden" name="volume" id="stl_volume" value="<?php echo $volume; ?>">
  <input type="hidden" name="printing_price" id="stl_printing_price" value="<?php echo $printing_price; ?>">
  <input type="hidden" name="printing_time" id="stl_printing_time" value="<?php echo $formatted_time; ?>">
  <input type="hidden" name="file_url" id="stl_file_url" value="<?php echo $upload['url']; ?>">
  <input type="hidden" name="original_file_name" id="stl_file_name" value="<?php echo $original_file_name; ?>">
</div>