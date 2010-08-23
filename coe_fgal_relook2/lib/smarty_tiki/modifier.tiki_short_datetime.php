<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once $smarty->_get_plugin_filepath('modifier','tiki_date_format');
function smarty_modifier_tiki_short_datetime($string, $intro='') {
	global $prefs;

	$date = smarty_modifier_tiki_date_format($string, $prefs['short_date_format']);
	$time = smarty_modifier_tiki_date_format($string, $prefs['short_time_format']);
	
	if( $prefs['tiki_same_day_time_only'] == 'y' && $date == smarty_modifier_tiki_date_format( time(), $prefs['short_date_format'] ) ) {
		//tra('on') tra('on:') tra('at') tra('at:')
		return empty($intro)? $time: str_replace(array('on', 'On'), array('at', 'At'), $intro).' '.$time;
	} else {
		$time = $date . ' ' . $time;
		return empty($intro)? $time: tra($intro).' '.$time;
	}
}
