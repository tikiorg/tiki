<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/tag.frame.inc.php,v 1.1 2008-01-15 09:21:13 mose Exp $

function guess_lengths($lengths_src, $total) {
  // Initialization
  $lengths = explode(",",$lengths_src);
  $values = array();
  foreach ($lengths as $length) { $values[] = 0; };

  // First pass: fixed-width columns
  for($i = 0; $i < count($lengths); $i++) {
    $length_src = trim($lengths[$i]);
    if (substr($length_src,strlen($length_src)-1,1) == "%") {
      // Percentage
      $values[$i] = $total * substr($length_src, 0, strlen($length_src)-1) / 100;
    } elseif (substr($length_src,strlen($length_src)-1,1) != "*") {
      // Pixels 
      global $g_media;
      $values[$i] = $length_src / $g_media->PPM();
    };
  };

  // Second pass: relative-width columns
  $rest = $total - array_sum($values);
  //  foreach ($values as $value) { $rest -= $value; };

  $parts = 0;
  foreach ($lengths as $length_src) { 
    if (substr($length_src,strlen($length_src)-1,1) == "*") { 
      $parts += max(1,substr($length_src,0,strlen($length)-1));
    };
  };

  if ($parts > 0) {
    $part_size = $rest / $parts;

    for ($i = 0; $i < count($lengths); $i++) {
      $length = $lengths[$i];

      if (substr($length,strlen($length)-1,1) == "*") { 
        $values[$i] = $part_size * max(1,substr($length,0,strlen($length)-1));
      };
    };
  };

  // Fix over/underconstrained framesets
  $width = array_sum($values);
  //  foreach ($values as $value) { $width += $value; };

  if ($width > 0) {
    $koeff = $total / $width;
    for($i = 0; $i < count($values); $i++) {
      $values[$i] *= $koeff;
    };
  };

  return $values;
}

?>