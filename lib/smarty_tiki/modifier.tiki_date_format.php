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
 * Name:     tiki_date_format
 * Purpose:  format datestamps via strftime, (timezone adjusted to administrator specified timezone)
 * Input:    string: input date string
 *           format: strftime format for output
 *           default_date: default date if $string is empty
 * -------------------------------------------------------------
 */
require_once $smarty->_get_plugin_filepath('shared','make_timestamp');
function smarty_modifier_tiki_date_format($string, $format = "%b %e, %Y", $default_date=null, $tra_format=null)
{
	global $tikilib, $user;
	$dc =& $tikilib->get_date_converter($user);
	
	$disptime = $dc->getDisplayDateFromServerDate($string);

	global $language;
	if ($tikilib->get_preference("language", "en") != $language && $tra_format) {
		$format = $tra_format;
	}
	
	if ($tikilib->get_display_offset($user)) {
		$format = preg_replace("/[ ]?%Z/","",$format);
	}
	else {
		$format = preg_replace("/%Z/","UTC",$format);
	}
	
	$date = new Date($disptime);
	return $date->format($format);
}

/* vim: set expandtab: */

?>
