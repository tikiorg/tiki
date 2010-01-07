<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// this returns the compact ISO 8601 date for microformats
function smarty_modifier_compactisodate($string) {
	global $tikilib;
	return $tikilib->get_compact_iso8601_datetime($string);
}
