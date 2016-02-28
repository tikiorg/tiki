<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_modifier_tiki_short_datetime($string, $intro='', $same='y')
{
	global $prefs;
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_modifier_tiki_date_format');
	$date = smarty_modifier_tiki_date_format($string, $prefs['short_date_format']);
	$time = smarty_modifier_tiki_date_format($string, $prefs['short_time_format']);

	$intro = !empty($intro) ?: tra($intro) . ' ';

	if ($prefs['jquery_timeago'] === 'y' && $same === 'y') {
		TikiLib::lib('header')->add_jq_onready('$("time.timeago").timeago();');
		return '<time class="timeago" datetime="' . TikiLib::date_format('c', $string, false, 5, false) .  '">' . $date . ' ' . $time . '</time>';
	} else if ( $same != 'n' && $prefs['tiki_same_day_time_only'] == 'y' && $date == smarty_modifier_tiki_date_format(time(), $prefs['short_date_format']) ) {
		//tra('on') tra('on:') tra('at') tra('at:')
		return str_replace(array('on', 'On'), array('at', 'At'), $intro) . $time;
	} else {
		// if you change the separator do not forget to change the translation instruction in lib/prefs/short.php
		$time = $date . ' ' . $time;
		return $intro . ' ' . $time;
	}
}
