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

<input type="hidden" name="volume" id="stl_volume" value="<?php echo $volume; ?>">
<input type="hidden" name="printing_price" id="stl_printing_price" value="<?php echo $printing_price; ?>">
<input type="hidden" name="printing_time" id="stl_printing_time" value="<?php echo $formatted_time; ?>">
<input type="hidden" name="file_path" id="stl_file_path" value="<?php echo $file_path; ?>">

