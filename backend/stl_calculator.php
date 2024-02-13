<?php

class STLCalc {

  // Properties
  private $volume;
  private $triangles_count;
  private $triangles_data;
  private $b_binary;
  private $points;
  private $fstl_handle;
  private $fstl_path;
  private $triangles;
  private $flag = false;

  // Initialises the STLCalc class by passing the path to the binary .stl file.
  function __construct($filepath) {
    $b = $this->isAscii($filepath);
    if (!$b) {
      $this->b_binary = TRUE;
      $this->fstl_handle = fopen($filepath, 'rb');
      $this->fstl_path = $filepath;
    }
    $this->triangles = array();
  }

  // Returns the calculated Volume (cc) of the 3D object represented in the binary STL. If $unit is 'cm' then returns volume in cubic cm, but If $unit is 'inch' then returns volume in cubic inches.

  function isAscii($filename): bool {
    $b = FALSE;
    $namePattern = '/facet\\s+normal\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+'
      . 'outer\\s+loop\\s+'
      . 'vertex\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+'
      . 'vertex\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+'
      . 'vertex\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+([-+]?\\b(?:[0-9]*\\.)?[0-9]+(?:[eE][-+]?[0-9]+)?\\b)\\s+'
      . 'endloop\\s+' . 'endfacet/';
    $fdata = file_get_contents($filename);
    preg_match_all($namePattern, $fdata, $matches);
    if (count($matches[0]) > 0) {
      $b = TRUE;
      $this->triangles_data = $matches;
    }
    return $b;
  }


  public function getVolume($unit) {
    if (!$this->flag) {
      $v = $this->calculateVolume();
      $this->volume = $v;
      $this->flag = true;
    }
    $volume = 0;
    if ($unit == 'cm') {
      $volume = ($this->volume / 1000);
    } elseif ($unit == 'mm') {
      $volume = $this->volume;
    } else {
      $volume = $this->inch3($this->volume / 1000);
    }
    return number_format((float)$volume, 2, '.', '') . " cubic cm";
  }

  // Sets the Density (gm/cc) of the material.

  private function calculateVolume() {
    $totalVolume = 0;
    if ($this->b_binary) {
      $totbytes = filesize($this->fstl_path);
      $totalVolume = 0;
      try {
        $this->readHeader();
        $this->triangles_count = $this->readTrianglesCount();
        $totalVolume = 0;
        try {
          while (ftell($this->fstl_handle) < $totbytes) {
            $totalVolume += $this->readTriangle();
          }
        } catch (Exception $e) {
          return $e;
        }
      } catch (Exception $e) {
        return $e;
      }
      fclose($this->fstl_handle);
    } else {
      $k = 0;
      while (count($this->triangles_data[4]) > 0) {
        $totalVolume += $this->readTriangleAscii();
        $k += 1;
      }
      $this->triangles_count = $k;
    }
    return abs($totalVolume);
  }

  function readHeader() {
    fseek($this->fstl_handle, ftell($this->fstl_handle) + 80);
  }

  // Invokes the binary file reader to read the header, serially reads all the normal vector and triangular co-ordinates, calls the math function to calculate signed tetrahedral volumes for each trangle, sums up these volumes to give the final volume of the 3D object represented in the .stl binary file.

  function readTrianglesCount() {
    $length = $this->phUnpack('I', 4);
    return $length[1];
  }

  // Wrapper around PHP's unpack() function which decodes binary numerical data to float, int, etc types. $sig specifies the type of data (i.e. integer, float, etc), $l specifies number of bytes to read.

  function phUnpack($sig, $l) {
    $s = fread($this->fstl_handle, $l);
    return unpack($sig, $s);
  }

  // Appends to an array either a single var or the contents of another array.

  function readTriangle() {
    $n = $this->phUnpack('f3', 12);
    $p1 = $this->phUnpack('f3', 12);
    $p2 = $this->phUnpack('f3', 12);
    $p3 = $this->phUnpack('f3', 12);
    $b = $this->phUnpack('v', 2);

    $l = count(array($this->points));
    return $this->signedVolumeOfTriangle($p1, $p2, $p3);
  }

  // Reads the binary header field in the STL file and offsets the file reader pointer to enable reading the triangle-normal data.

  function signedVolumeOfTriangle($p1, $p2, $p3) {
    $v321 = $p3[1] * $p2[2] * $p1[3];
    $v231 = $p2[1] * $p3[2] * $p1[3];
    $v312 = $p3[1] * $p1[2] * $p2[3];
    $v132 = $p1[1] * $p3[2] * $p2[3];
    $v213 = $p2[1] * $p1[2] * $p3[3];
    $v123 = $p1[1] * $p2[2] * $p3[3];
    return (1.0 / 6.0) * (-$v321 + $v231 + $v312 - $v132 - $v213 + $v123);
  }

  // Reads the binary field in the STL file which specifies the total number of triangles and returns that integer.

  function readTriangleAscii() {
    $p1[1] = floatval(array_pop($this->triangles_data[4]));
    $p1[2] = floatval(array_pop($this->triangles_data[5]));
    $p1[3] = floatval(array_pop($this->triangles_data[6]));
    $p2[1] = floatval(array_pop($this->triangles_data[7]));
    $p2[2] = floatval(array_pop($this->triangles_data[8]));
    $p2[3] = floatval(array_pop($this->triangles_data[9]));
    $p3[1] = floatval(array_pop($this->triangles_data[10]));
    $p3[2] = floatval(array_pop($this->triangles_data[11]));
    $p3[3] = floatval(array_pop($this->triangles_data[12]));
    return $this->signedVolumeOfTriangle($p1, $p2, $p3);
  }

  // Reads a triangle data from the binary STL and returns its signed volume. A binary STL is a representation of a 3D object as a collection of triangles and their normal vectors. Its specifiction can be found here: http://en.wikipedia.org/wiki/STL_(file_format)%23Binary_STL This function reads the bytes of the binary STL file, decodes the data to give float XYZ co-ordinates of the trinaglular vertices and the normal vector for a triangle.

  function inch3($v) {
    return $v * 0.0610237441;
  }

  // Reads a triangle data from the ascii STL and returns its signed volume.

  function phAppend($myarr, $mystuff) {
    if (gettype($mystuff) == 'array') {
      $myarr = array_merge($myarr, $mystuff);
    } else {
      $ctr = count($myarr);
      $myarr[$ctr] = $mystuff;
    }
    return $myarr;
  }

  public function calculatePrintingTime($volume, $infill_density, $layer_height): array {
    $printing_speed = get_option('ads_printing_speed') ?? false;
    $nozzle_diameter = get_option('ads_nozzle_diameter') ?? false;

    /**
     * 
     * Now user is sending layer height  
     *  $layer_height = get_option('ads_layer_heights') ? get_option('ads_layer_heights') : [0 => ''];
     */

//    $volume = ($volume * 0.25) + ($volume * 0.75 * $infill_density / 100);
    $time_in_seconds = intval($infill_density * $volume * 1000 / (floatval($printing_speed) * floatval($nozzle_diameter) * floatval($layer_height[0])));
    return [max($time_in_seconds, 60), $this->getFormattedTime($time_in_seconds)];
  }

  private function getFormattedTime($time_in_seconds): string {
    $minutes = ($time_in_seconds / 60) > 0 ? ($time_in_seconds / 60) % 60 : 0;
    $hours = ($time_in_seconds / 3600) > 0 ? (($time_in_seconds / 3600) % 60) % 24 : 0;
    $days = ($time_in_seconds / 86400) > 0 ? ($time_in_seconds / 86400) % 24 : 0;
    $formatted_time = "";
    if ($days > 0) {
      $formatted_time .= $days > 1 ? "$days Days, " : "$days Day ";
    }
    if ($hours > 0) {
      $formatted_time .= $hours > 1 ? "$hours Hours, " : "$hours Hour ";
    }
    if ($minutes > 0) {
      $formatted_time .= $minutes > 1 ? "$minutes Minutes" : "$minutes Minute";
    }
    return trim($formatted_time, ', ');
  }

  public function calculatePrintingPrice($time_in_seconds): string {
    $default_printing_price = get_option('ads_default_printing_price') ?? 0;
    $printing_price = get_option('ads_printing_price') ?? 0;
    return number_format((float)($default_printing_price + ($printing_price * ($time_in_seconds / 60))), 2, '.', ',');
  }
}
