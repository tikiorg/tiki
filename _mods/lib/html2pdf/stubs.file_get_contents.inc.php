<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/stubs.file_get_contents.inc.php,v 1.1 2008-01-15 09:21:12 mose Exp $
if (!function_exists('file_get_contents')) {
function file_get_contents($file) {
  $lines = file($file);
  if ($lines) {
    return implode('',$lines);
  } else {
    return "";
  };
}
}
?>
