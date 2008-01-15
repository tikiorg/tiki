<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/ps.text.inc.php,v 1.1 2008-01-15 09:21:08 mose Exp $

// Initialize utf-8 symbols to different encodings mappings
$g_utf8_to_encodings_mapping = array();
foreach (array_keys($g_utf8_converters) as $encoding) {
  $flipped = array_flip($g_utf8_converters[$encoding][0]);
  foreach ($flipped as $utf => $code) {
    if (ord($code)>=32 && ord($code)<128) {
      $g_utf8_to_encodings_mapping[$utf][$encoding] = $code;
    } else {
      $g_utf8_to_encodings_mapping[$utf][$encoding] = sprintf("\\%03o",ord($code));
    };
  };
};

// FIXME: just a workaround for now; these arrays should be joined
$g_utf8_to_encodings_mapping_pdf = array();
foreach (array_keys($g_utf8_converters) as $encoding) {
  $flipped = array_flip($g_utf8_converters[$encoding][0]);
  foreach ($flipped as $utf => $code) {
    $g_utf8_to_encodings_mapping_pdf[$utf][$encoding] = $code;
  };
};

function quote_ps($psdata) {
  $str = str_replace("\\", "\\\\", $psdata);
  $str = str_replace(array("(",")","%"), array("\\(","\\)","\\%"), $str);

  // Replace characters having 8-bit set with their octal representation
  for ($i=0; $i<strlen($str); $i++) {
    if (ord($str{$i}) > 127) {
      $str = substr_replace($str, sprintf("\\%o", ord($str{$i})), $i, 1);
      $i += 3;
    };
  };
  
  return $str;
}

?>