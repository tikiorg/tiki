<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     date_format
 * Purpose:  format datestamps via strftime no timezone adjustment
 * Input:    string: user time stamp
 *           format: strftime format for output
 * -------------------------------------------------------------
 */
require_once $smarty->_get_plugin_filepath('shared','make_timestamp');
function smarty_modifier_user_date_format($string, $format = "%B %e, %Y")
{
	
	$date = new Date($string);
	return $date->format(tra($format));
}

?>
