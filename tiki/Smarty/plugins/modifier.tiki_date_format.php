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

	if (!isset($string) || strlen($string) == 0) {
		if (!isset($default_date) || strlen($default_date) == 0)
			return;
		$string = $default_date;
	}
	
	$timestamp = smarty_make_timestamp($string);

	if (!$tikilib)
		return strftime($format, $timestamp);

	static $localed = false;
	if (!$localed) {
		$localed = true;
#		@setlocale(LC_TIME, $tikilib->get_locale($user));
	}

	require_once('lib/Date.php');

	$original_tz = date('T', $timestamp);
	
$rv = "\n<pre>\n";
$rv .= strftime($format, $timestamp);
$rv .= " =timestamp\n";
$rv .= strftime('%Z', $timestamp);
$rv .= " =strftime('%Z')\n";
$rv .= date('T', $timestamp);
$rv .= " =date('T')\n";

	$date = new Date($timestamp);

# Calling new Date() changes the timezone of the $timestamp var!
# so we only change the timezone to UTC if the original TZ wasn't UTC
# to begin with.
# This seems really buggy, but I don't have time to delve into right now.

$rv .= date('T', $timestamp);
$rv .= " =date('T')\n";

$rv .= $date->format($format);
$rv .= " =new Date()\n";

$rv .= date('T', $timestamp);
$rv .= " =date('T')\n";
	
	if ($original_tz == 'UTC') {
		$date->setTZbyID('UTC');
$rv .= $date->format($format);
$rv .= " =setTZbyID('UTC')\n";
	}
	
	$tz_id = $tikilib->get_display_timezone($user);
	if ($date->tz->getID() != $tz_id) {
		# let's convert to the displayed timezone
		$date->convertTZbyID($tz_id);
$rv .= $date->format($format);
$rv .= " =convertTZbyID($tz_id)\n";
	}

#return $rv;

#	if ($format == "%b %e, %Y")
#		$format = $tikilib->get_short_date_format();
   	return $date->format($format);
}

/* vim: set expandtab: */

?>
