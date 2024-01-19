<?php
function duplicate_array(): array {
  $duplicate = [];
  $uni = [];
  foreach ($_POST['layer_heights'] as $value) {
    if (isset($uni[$value])) {
      $duplicate[$value] = $value;
    } else {
      $uni[$value] = $value;
    }
  }
  return $duplicate;
}

