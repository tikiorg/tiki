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
 * Purpose:  format datestamps via Pear::Date using TikiLib static call, (timezone adjusted to user specified timezone)
 * Input:    string: input date string
 *           format: strftime format for output
 * -------------------------------------------------------------
 */
function smarty_modifier_tiki_date_format($string, $format) {
	return TikiLib::date_format($format,$string);
}

?>
