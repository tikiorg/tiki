<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once $smarty->_get_plugin_filepath('modifier','tiki_date_format');

function smarty_modifier_tiki_short_datetime($string)
{
	global $tikilib;
	return smarty_modifier_tiki_date_format($string, $tikilib->get_short_datetime_format(), null, tra("%a %d of %b, %Y[%H:%M %Z]"));
}

/* vim: set expandtab: */

?>
