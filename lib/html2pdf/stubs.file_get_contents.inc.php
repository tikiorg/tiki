<?php
// $Header: /cvsroot/tikiwiki/lib/html2pdf/stubs.file_get_contents.inc.php,v 1.1.1.1 2006-02-06 15:39:46 nikchankov Exp $

function file_get_contents($file) {
  $lines = file($file);
  if ($lines) {
    return implode('',$lines);
  } else {
    return "";
  };
}
?>