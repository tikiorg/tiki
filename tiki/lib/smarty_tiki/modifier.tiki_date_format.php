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
require_once $smarty->_get_plugin_filepath('shared','make_timestamp');
function smarty_modifier_tiki_date_format($string, $format = "%b %e, %Y", $default_date=null)
{
	global $tikilib, $user;
    $dc =& $tikilib->get_date_converter($user);

    $disptime = $dc->getDisplayDateFromServerDate($string);
    if ($dc->getTzName() != "UTC") $format = preg_replace("/ ?%Z/","",$format);
    else $format = preg_replace("/%Z/","UTC",$format);

    // strftime doesn't do translations right
	//return strftime($format, $disptime);

    $date = new Date($disptime);
    return $date->format($format);
}

/* vim: set expandtab: */

?>
