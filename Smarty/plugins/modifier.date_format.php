<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     date_format
 * Purpose:  format datestamps via strftime
 * Input:    string: input date string
 *           format: strftime format for output
 *           default_date: default date if $string is empty
 * -------------------------------------------------------------
 */
#require_once $this->_get_plugin_filepath('shared','make_timestamp');

require_once $this->_get_plugin_filepath('modifier','tiki_date_format');
function smarty_modifier_date_format($string, $format="%b %e, %Y", $default_date=null)
{
/*
Per Luis's request:

Please make sure to change the date_format smarty plugin so if someone
decides to "invent" his own date
format then dates will still be corrected according to the offset. For
example a developer creating the calendar may want
to display only the day for today in a huge-blue font he will put in his
template $today|date_format:"d" or similar. In
the date_format Smarty plugin we should adjust the date using the offset.
*/
	return smarty_modifier_tiki_date_format($string, $format, $default_date);

#	if($string != '') {
#    	return strftime($format, smarty_make_timestamp($string));
#	} elseif (isset($default_date) && $default_date != '') {		
#    	return strftime($format, smarty_make_timestamp($default_date));
#	} else {
#		return;
#	}

}

/* vim: set expandtab: */

?>
