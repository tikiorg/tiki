<?php

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
require_once $this->_get_plugin_filepath('shared','make_timestamp');
function smarty_modifier_tiki_date_format($string, $format = "%b %e, %Y", $default_date=null)
{
	global $tikilib, $user;

	require_once('lib/Date.php');
	
	if (!isset($string) || strlen($string) == 0) {
		if (!isset($default_date) || strlen($default_date) == 0)
			return;
		$string = $default_date;
	}
	
	if (!$tikilib)
		return strftime($format, smarty_make_timestamp($string));

	$timestamp = smarty_make_timestamp($string);

	@setlocale(LC_TIME, $tikilib->get_locale($user));

	$date = new Date($timestamp);
	$date->convertTZbyID($tikilib->get_display_timezone($user));
#	if ($format == "%b %e, %Y")
#		$format = $tikilib->get_short_date_format();
   	return $date->format($format);
}

/* vim: set expandtab: */

?>
