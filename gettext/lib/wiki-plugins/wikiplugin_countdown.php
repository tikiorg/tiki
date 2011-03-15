<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Tiki-Wiki Countdown plugin
 *
 * This is an example plugin to indicate a countdown to a date.
 * Plugins are called using the syntax
 * {COUNTDOWN(end=>string date)} to reach all targets!{COUNTDOWN}
 * Name must be in uppercase!
 * params is in the form: name=>value,name2=>value2 (don't use quotes!)
 * If the plugin doesn't use params use {NAME()}content{NAME}
 *
 * The function will receive the plugin content in $data and the params
 * in the asociative array $params (using extract to pull the arguments
 * as in the example is a good practice)
 * The function returns some text that will replace the content in the
 * wiki page.
 */
function wikiplugin_countdown_help() {
	return tra("Example").":<br />~np~{COUNTDOWN(enddate=>April 1 2004[,locatetime=>on])}".tra("text")."{COUNTDOWN}~/np~";
}

function wikiplugin_countdown_info() {
	return array(
		'name' => tra('Countdown'),
		'documentation' => 'PluginCountdown',
		'description' => tra('Display a countdown to a specified date.'),
		'prefs' => array('wikiplugin_countdown'),
		'icon' => 'pics/icons/clock.png',
		'body' => tra('Text to append to the countdown.'),
		'params' => array(
			'enddate' => array(
				'required' => true,
				'name' => tra('End date'),
				'description' => tra('Target date. Multiple formats accepted.'),
				'default' => '',
			),
			'locatetime' => array(
				'required' => false,
				'name' => tra('Locate Time'),
				'description' => tra('Set to off to only show days, otherwise hours, minutes and seconds are also shown (all are shown by default)'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('On'), 'value' => 'on'), 
					array('text' => tra('Off'), 'value' => 'off')
				),
			),
			'show' => array(
				'required' => false,
				'name' => tra('Items to Show'),
				'description' => tra('Select: d=days, h=hours, m=minutes, s=seconds. Enter multiple values as: dhms. If blank, all items are shown.'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Days'), 'value' => 'd'), 
					array('text' => tra('Days & Hours'), 'value' => 'dh'), 
					array('text' => tra('Days, Hours & Minutes'), 'value' => 'dhm'), 
					array('text' => tra('Days, Hours, Minutes & Seconds'), 'value' => 'dhms'), 
					array('text' => tra('Hours'), 'value' => 'h'),
					array('text' => tra('Hours & Minutes'), 'value' => 'hm'),
					array('text' => tra('Hours, Minutes & Seconds'), 'value' => 'hms'),
					array('text' => tra('Minutes'), 'value' => 'm'),
					array('text' => tra('Minutes & Seconds'), 'value' => 'ms'),
					array('text' => tra('Seconds'), 'value' => 's'),
				),
			),
			'since' => array(
				'required' => false,
				'name' => tra('Show time since event'),
				'description' => tra('If y, will display amount of time since the event (default). If n will simply display that the event is over.'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
		),
	);
}

function wikiplugin_countdown($data, $params) {
	global $tikilib, $tikidate;
	extract ($params,EXTR_SKIP);
	$ret = '';

	if (!isset($enddate)) {
		return ("<strong>COUNTDOWN: Missing 'enddate' parameter for plugin</strong><br />");
	}

	if (!isset($show)){
		// Set default. If no explicit SHOW, then show everything.
		$show_days = 'y';
		$show_hours = 'y';
		$show_minutes = 'y';
		$show_seconds = 'y';
	} else {
		$show_days = 'd';
		$show_hours = 'h';
		$show_minutes = 'm';
		$show_seconds = 's';

		// Which items to show? Day, Hour, Minute, Seconds
		$show_test = strpos($show, $show_days);
		if ($show_test === false) {
			$show_days = 'n';
		} else {
			$show_days = 'y';
		}
		$show_test = strpos($show, $show_hours);
		if ($show_test === false) {
			$show_hours = 'n';
		} else {
			$show_hours = 'y';
		}
		$show_test = strpos($show, $show_minutes);
		if ($show_test === false) {
			$show_minutes = 'n';
		} else {
			$show_minutes = 'y';
		}
		$show_test = strpos($show, $show_seconds);
		if ($show_test === false) {
			$show_seconds = 'n';
		} else {
			$show_seconds = 'y';
		}
	}	
	
	// Parse the string and cancel the server environment's timezone adjustment
	$then = strtotime($enddate) + date('Z');

	// Calculate the real UTC timestamp
	//  (the string was specified using the user timezone)
	$tikidate->setTZbyID($tikilib->get_display_timezone());
	$tikidate->setDate($then);
	$tikidate->setTZbyID('UTC');
	$then = $tikidate->getTime();

	$difference = $then - $tikilib->now;
	// Determine if the event is over (has past)
	if ($difference >= 0) {
		$is_over = 'n';
		} else {
		$is_over = 'y';
		}
	
	$num = $difference/86400;
	$days = intval($num);
	$num2 = ($num - $days)*24;
	$hours = intval($num2);
	$num3 = ($num2 - $hours)*60;
	$mins = intval($num3);
	$num4 = ($num3 - $mins)*60;
	$secs = intval($num4);

if (($is_over == 'n') or (($is_over == 'y') and ($since !== 'n')))
	{ 
	// Show time remaining (if not over) or time since (if over AND since)
	// Use absolute values to avoid negative numbers
	if ($show_days == 'y') {
		$days = abs($days);
		$ret .= "$days ".tra("days").", ";
	}
	if (empty($locatetime) || $locatetime != 'off') {
		if ($show_hours == 'y') {
			$hours = abs($hours);
			$ret .= "$hours ".tra("hours").", ";
		}
		if ($show_minutes == 'y') {
			$mins = abs($mins);
			$ret .= "$mins ".tra("minutes").", ";
		}
		if ($show_seconds == 'y') {
			$secs = abs ($secs);
			$ret .= "$secs ".tra("seconds").", ";
		}
	}
	if ($is_over == 'y') {
		$ret .= tra("since ").$data;
		} else {		
		$ret .= tra("until ").$data;
		}
}
	
	if (($is_over == 'y') and ($since == 'y')) {
			$ret .= $data.tra(" is over");
	}

	return $ret;
}
