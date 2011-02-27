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

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_select_time} function plugin
 *
 * Type:     function<br>
 * Name:     html_select_time<br>
 * Purpose:  Prints the dropdowns for time selection
 * @link http://smarty.php.net/manual/en/language.function.html.select.time.php {html_select_time}
 *          (Smarty online manual)
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_make_timestamp()
 */
function smarty_function_html_select_time($params, &$smarty)
{
	global $tikilib;
	require_once $smarty->_get_plugin_filepath('shared','make_timestamp');
	require_once $smarty->_get_plugin_filepath('function','html_options');
	/* Default values. */
	$prefix             = "Time_";
	$time               = time();
	$display_hours      = true;
	$display_minutes    = true;
	$display_seconds    = true;
	$display_meridian   = true;
	$use_24_hours       = true;
	$minute_interval    = 1;
	$second_interval    = 1;
	$hour_minmax        = '0-23';
	/* Should the select boxes be part of an array when returned from PHP?
	   e.g. setting it to "birthday", would create "birthday[Hour]",
	   "birthday[Minute]", "birthday[Seconds]" & "birthday[Meridian]".
	   Can be combined with prefix. */
	$field_array        = null;
	$all_extra          = null;
	$hour_extra         = null;
	$minute_extra       = null;
	$second_extra       = null;
	$meridian_extra     = null;
	$hour_empty = null;
	$minute_empty = null;
	$second_empty = null;
	$all_empty = null;

	extract($params);
	if (!empty($all_empty)) {
		$hour_empty = $minute_empty = $second_empty = $all_empty;
	}

	if (!isset($time) or !$time) {
		$time = $tikilib->now;
	} else if (is_string($time) && strpos($time, ':') !== false) {
		$e = explode(':', $time, 3);
		$time = $tikilib->make_time(isset($e[0])?$e[0]:0, isset($e[1])?$e[1]:0, isset($e[2])?$e[2]:0, $tikilib->date_format('%m'), $tikilib->date_format('%d'), $tikilib->date_format('%Y'));
	}
	if (empty($hour_minmax) || !preg_match('/^[0-2]?[0-9]-[0-2]?[0-9]$/',$hour_minmax)) {
		$hour_minmax = '0-23';
	}
	//only needed for end_ and the static variable in the date_format functions seem to cause problems without the if
	if ($prefix == 'end_') {
		$time_hr24 = TikiLib::date_format('%H%M%s', $time);
	}
	
	$html_result = '';

	if ($display_hours) {
		if ($use_24_hours) {
			list($hour_min,$hour_max) = explode('-',$hour_minmax);
			$hours = range(($hour_min == 24 ? 0 : $hour_min), ($hour_max == 0 || $hour_max == 24 ? 23 : $hour_max));
			$hour_fmt = '%H';
			$latest = 23;
		//12-hour clock
		} else {
			$hours = range(1, 12);
			$hour_fmt = '%I';
			$latest = 11;
		}
		for ($i = 0, $for_max = count($hours); $i < $for_max; $i++)
			$hours[$i] = sprintf('%02d', $hours[$i]);
		if ($prefix == 'end_' && ($time_hr24 == '000000')) {
			$selected = $latest;
		} elseif ($prefix == 'duration_' || $prefix == 'startday_' || $prefix == 'endday_') {
			if ($use_24_hours) {
				$selected = floor($time / (60*60));
			} else {
				$selected = date('h', strtotime(floor($time / (60*60)) . ':00 '));
			}
		} else {
			$selected = $time == '--' ? $hour_empty : TikiLib::date_format($hour_fmt, $time);
		}
			$html_result .= '<select name=';
		if (null !== $field_array) {
			$html_result .= '"' . $field_array . '[' . $prefix . 'Hour]"';
		} else {
			$html_result .= '"' . $prefix . 'Hour"';
		}
		if (null !== $hour_extra){
			$html_result .= ' ' . $hour_extra;
		}
		if (null !== $all_extra){
			$html_result .= ' ' . $all_extra;
		}
		$html_result .= '>'."\n";
		if (!empty($hour_empty)) $hours = array_merge(array($hour_empty==' '?'':$hour_empty), $hours);
		$html_result .= smarty_function_html_options(array(
			'output'		=>	$hours,
			'values'		=>	$hours,
			'selected'		=>	$selected,
			'print_result'	=>	false
			),
			$smarty
		);
		$html_result .= "</select>\n";
	}

	if ($display_minutes) {
		$all_minutes = range(0, 59);
		for ($i = 0, $for_max = count($all_minutes); $i < $for_max; $i+= $minute_interval)
			$minutes[] = sprintf('%02d', $all_minutes[$i]);
			if ($minute_interval > 1) {
				$minutes[] = 59;
			}
		if ($prefix == 'end_' && ($time_hr24 == '000000' || strftime('%M', $time) == 59)) {
			$selected = 59;
		} else {
			$selected = $time == '--' ? $minute_empty : intval(floor(strftime('%M', $time) / $minute_interval) * $minute_interval);
		}
		$html_result .= '<select name=';
		if (null !== $field_array) {
			$html_result .= '"' . $field_array . '[' . $prefix . 'Minute]"';
		} else {
			$html_result .= '"' . $prefix . 'Minute"';
		}
		if (null !== $minute_extra){
			$html_result .= ' ' . $minute_extra;
		}
		if (null !== $all_extra){
			$html_result .= ' ' . $all_extra;
		}
		$html_result .= '>'."\n";
		if (!empty($minute_empty)) $minutes = array_merge(array($minute_empty==' '?'':$minute_empty), $minutes);
		$html_result .= smarty_function_html_options(array(
			'output'		=>	$minutes,
			'values'		=>	$minutes,
			'selected'		=>	$selected,
			'print_result'	=>	false
			),
			$smarty
			);
		$html_result .= "</select>\n";
	}

	if ($display_seconds) {
		$all_seconds = range(0, 59);
		for ($i = 0, $for_max = count($all_seconds); $i < $for_max; $i+= $second_interval)
			$seconds[] = sprintf('%02d', $all_seconds[$i]);
			if ($second_interval > 1) {
				$seconds[] = 59;
			}
		if ($prefix == 'end_' && ($time_hr24 ==  '000000' || strftime('%M', $time) == 59)) {
			$selected = 59;
		} else {
			$selected = $time =='--'?$second_empty:intval(floor(strftime('%S', $time) / $second_interval) * $second_interval);
		}
		$html_result .= '<select name=';
		if (null !== $field_array) {
			$html_result .= '"' . $field_array . '[' . $prefix . 'Second]"';
		} else {
			$html_result .= '"' . $prefix . 'Second"';
		}
		
		if (null !== $second_extra){
			$html_result .= ' ' . $second_extra;
		}
		if (null !== $all_extra){
			$html_result .= ' ' . $all_extra;
		}
		$html_result .= '>'."\n";
		if (!empty($seconde_empty)) $secondes = array_merge(array($seconde_empty==' '?'':$seconde_empty), $secondes);
		$html_result .= smarty_function_html_options(array(
			'output'		=>	$seconds,
			'values'		=>	$seconds,
			'selected'		=>	$selected,
			'print_result'	=>	false
			),
			$smarty
		);
		$html_result .= "</select>\n";
	}
	if (!$use_24_hours) {
		$html_result .= '<select name=';
		if (null !== $field_array) {
			$html_result .= '"' . $field_array . '[' . $prefix . 'Meridian]"';
		} else {
			$html_result .= '"' . $prefix . 'Meridian"';
		}
		
		if (null !== $meridian_extra){
			$html_result .= ' ' . $meridian_extra;
		}
		if (null !== $all_extra){
			$html_result .= ' ' . $all_extra;
		}
		$html_result .= '>'."\n";
		
		$html_result .= smarty_function_html_options(array(
			'output'		=>	array('AM', 'PM'),
			'values'		=>	array('am', 'pm'),
			'selected'		=>	TikiLib::date_format('%p', $time),
			'print_result'	=>	false
			),
			$smarty
		);
		$html_result .= "</select>\n";
	}

	// date: 2003/02/12 21:23:52;  author: gilshwartz;  state: Exp;  lines: +1 -1
	// Enforce LTR direction of time entry regardless of overall directionality.
	// -    print $html_result;
	// +    print '<span dir="ltr">'.$html_result.'</span>';
	
	$html_result = '<span dir="ltr">' . $html_result . '</span>';
	return $html_result;
}
