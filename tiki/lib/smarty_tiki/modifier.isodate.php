<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// this returns the ISO 8601 date for microformats
function smarty_modifier_isodate($string) {
	global $tikilib;
	return $tikilib->get_iso8601_datetime($string);
}

?>