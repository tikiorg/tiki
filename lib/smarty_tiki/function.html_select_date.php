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

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {html_select_date} plugin
 *
 * Type:     function<br>
 * Name:     html_select_date<br>
 * Purpose:  Prints the dropdowns for date selection.
 *
 * ChangeLog:<br>
 *           - 1.0 initial release
 *           - 1.1 added support for +/- N syntax for begin
 *                and end year values. (Monte)
 *           - 1.2 added support for yyyy-mm-dd syntax for
 *                time value. (Jan Rosier)
 *           - 1.3 added support for choosing format for
 *                month values (Gary Loescher)
 *           - 1.3.1 added support for choosing format for
 *                day values (Marcus Bointon)
 *           - 1.3.2 support negative timestamps, force year
 *             dropdown to include given date unless explicitly set (Monte)
 *           - 1.3.4 fix behaviour of 0000-00-00 00:00:00 dates to match that
 *             of 0000-00-00 dates (cybot, boots)
 * @link http://smarty.php.net/manual/en/language.function.html.select.date.php {html_select_date}
 *      (Smarty online manual)
 * @version 1.3.4
 * @author Andrei Zmievski
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_html_select_date($params, $smarty)
{
	global $tikilib, $prefs; // TIKI

	$smarty->loadPlugin('smarty_shared_escape_special_chars');
	$smarty->loadPlugin('smarty_shared_make_timestamp');
	$smarty->loadPlugin('smarty_function_html_options');

	/* Default values. */
	$prefix          = "Date_";
	$start_year      = $prefs['display_start_year'];
	$end_year        = $prefs['display_end_year'];
	$display_days    = true;
	$display_months  = true;
	$display_years   = true;
	$month_format    = "%B";

	/* Write months as numbers by default  GL */
	$month_value_format = "%m";
	$day_format      = "%02d";

	/* Write day values using this format MB */
	$day_value_format = "%d";
	$year_as_text    = false;

	/* Display years in reverse order? Ie. 2000,1999,.... */
	$reverse_years   = false;

	/* Should the select boxes be part of an array when returned from PHP?
		 e.g. setting it to "birthday", would create "birthday[Day]",
		 "birthday[Month]" & "birthday[Year]". Can be combined with prefix */
	$field_array     = null;

	/* <select size>'s of the different <select> tags.
		 If not set, uses default dropdown. */
	$day_size        = null;
	$month_size      = null;
	$year_size       = null;

	/* Unparsed attributes common to *ALL* the <select>/<input> tags.
		 An example might be in the template: all_extra ='class ="foo"'. */
	$all_extra       = null;

	/* Separate attributes for the tags. */
	$day_extra       = null;
	$month_extra     = null;
	$year_extra      = null;

	/* Order in which to display the fields.
		 "D" -> day, "M" -> month, "Y" -> year. */
	$field_order     = $prefs['display_field_order'];

	/* String printed between the different fields. */
	$field_separator = "\n";
	$time = time();
	$all_empty       = null;
	$day_empty       = null;
	$month_empty     = null;
	$year_empty      = null;
	$extra_attrs     = '';

	foreach ($params as $_key => $_value) {
		switch ($_key) {
			case 'prefix':
			case 'time':
			case 'start_year':
			case 'end_year':
			case 'month_format':
			case 'day_format':
			case 'day_value_format':
			case 'field_array':
			case 'day_size':
			case 'month_size':
			case 'year_size':
			case 'all_extra':
			case 'day_extra':
			case 'month_extra':
			case 'year_extra':
			case 'field_order':
			case 'field_separator':
			case 'month_value_format':
			case 'month_empty':
			case 'day_empty':
			case 'year_empty':
				$$_key = (string) $_value;
				break;

			case 'all_empty':
				$$_key = (string) $_value;
				$day_empty = $month_empty = $year_empty = $all_empty;
				break;

			case 'display_days':
			case 'display_months':
			case 'display_years':
			case 'year_as_text':
			case 'reverse_years':
				$$_key = (bool) $_value;
				break;

			default:
				if (!is_array($_value)) {
					$extra_attrs .= ' '.$_key.'="'.smarty_function_escape_special_chars($_value).'"';
				} else {
					trigger_error("html_select_date: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
				}
				break;
		}
	}

	if (preg_match('!^-\d+$!', $time)) {
		// negative timestamp, use date()
		$time = date('Y-m-d', $time);
	}
	// If $time is not in format yyyy-mm-dd
	if (preg_match('/^(\d{0,4}-\d{0,2}-\d{0,2})/', $time, $found)) {
		$time = $found[1];
	} else {
		// TIKI: use tikilib date_format to format as yyyy-mm-dd
		$time = $tikilib->date_format('%Y-%m-%d', $time);
	}
	// Now split this in pieces, which later can be used to set the select
	$time = explode("-", $time);

	// make syntax "+N" or "-N" work with start_year and end_year
	if (preg_match('!^(\+|\-)\s*(\d+)$!', $end_year, $match)) {
		if ($match[1] == '+') {
			$end_year = strftime('%Y') + $match[2];
		} else {
			$end_year = strftime('%Y') - $match[2];
		}
	}
	if (preg_match('!^(\+|\-)\s*(\d+)$!', $start_year, $match)) {
		if ($match[1] == '+') {
			$start_year = strftime('%Y') + $match[2];
		} else {
			$start_year = strftime('%Y') - $match[2];
		}
	}
	if (strlen($time[0]) > 0) {
		if ($start_year > $time[0] && !isset($params['start_year'])) {
			// force start year to include given date if not explicitly set
			$start_year = $time[0];
		}
		if ($end_year < $time[0] && !isset($params['end_year'])) {
			// force end year to include given date if not explicitly set
			$end_year = $time[0];
		}
	}

	$field_order = strtoupper($field_order);

	$html_result = $month_result = $day_result = $year_result = "";

	$field_separator_count = -1;
	if ($display_months) {
		$field_separator_count++;
		$month_names = array();
		$month_values = array();
		if (isset($month_empty)) {
			$month_names[''] = $month_empty;
			$month_values[''] = '';
		}
		for ($i = 1; $i <= 12; $i++) {
			// TIKI: translation
			//tra('January') tra('February') tra('March') tra('April') tra('May') tra('June')
			// tra('July') tra('August') tra('September') tra('October') tra('November') tra('December')

			$month_names[$i] = ucfirst(tra(strftime($month_format, mktime(0, 0, 0, $i, 1, 2000))));
			$month_values[$i] = strftime($month_value_format, mktime(0, 0, 0, $i, 1, 2000));
		}

		$month_result .= '<select class="form-control date" name=';
		if (null !== $field_array) {
			$month_result .= '"' . $field_array . '[' . $prefix . 'Month]"';
		} else {
			$month_result .= '"' . $prefix . 'Month"';
		}
		if (null !== $month_size) {
			$month_result .= ' size="' . $month_size . '"';
		}
		if (null !== $month_extra) {
			$month_result .= ' ' . $month_extra;
		}
		if (null !== $all_extra) {
			$month_result .= ' ' . $all_extra;
		}
		$month_result .= $extra_attrs . '>' . "\n";

		$month_result .= smarty_function_html_options(
			array(
				'output'     => $month_names,
				'values'     => $month_values,
				'selected'   => (int) $time[1] ? strftime($month_value_format, mktime(0, 0, 0, (int) $time[1], 1, 2000)) : '',
				'print_result' => false
			),
			$smarty
		);
		$month_result .= '</select>';
	}

	if ($display_days) {
		$field_separator_count++;
		$days = array();
		if (isset($day_empty)) {
			$days[''] = '';
			$day_values[''] = $day_empty;
		}
		for ($i = 1; $i <= 31; $i++) {
			$days[] = sprintf($day_format, $i);
			$day_values[] = sprintf($day_value_format, $i);
		}

		$day_result .= '<select class="form-control date" name=';
		if (null !== $field_array) {
			$day_result .= '"' . $field_array . '[' . $prefix . 'Day]"';
		} else {
			$day_result .= '"' . $prefix . 'Day"';
		}
		if (null !== $day_size) {
			$day_result .= ' size="' . $day_size . '"';
		}
		if (null !== $all_extra) {
			$day_result .= ' ' . $all_extra;
		}
		if (null !== $day_extra) {
			$day_result .= ' ' . $day_extra;
		}
		$day_result .= $extra_attrs . '>'."\n";
		$day_result .= smarty_function_html_options(
			array(
				'output'       => $day_values,
				'values'       => $days,
				'selected'     => $time[2],
				'print_result' => false
			),
			$smarty
		);
		$day_result .= '</select>';
	}

	if ($display_years) {
		$field_separator_count++;
		if (null !== $field_array) {
			$year_name = $field_array . '[' . $prefix . 'Year]';
		} else {
			$year_name = $prefix . 'Year';
		}
		if ($year_as_text) {
			$year_result .= '<input type="text" name="' . $year_name . '" value="' . $time[0] . '" size="4" maxlength="4"';
			if (null !== $all_extra) {
				$year_result .= ' ' . $all_extra;
			}
			if (null !== $year_extra) {
				$year_result .= ' ' . $year_extra;
			}
			$year_result .= ' />';
		} else {
			$years = range((int) $start_year, (int) $end_year);
			if ($reverse_years) {
				rsort($years, SORT_NUMERIC);
			} else {
				sort($years, SORT_NUMERIC);
			}
			$yearvals = $years;
			if (isset($year_empty)) {
				array_unshift($years, $year_empty);
				array_unshift($yearvals, '');
			}
			$year_result .= '<select class="form-control date" name="' . $year_name . '"';
			if (null !== $year_size) {
				$year_result .= ' size="' . $year_size . '"';
			}
			if (null !== $all_extra) {
				$year_result .= ' ' . $all_extra;
			}
			if (null !== $year_extra) {
				$year_result .= ' ' . $year_extra;
			}
			$year_result .= $extra_attrs . '>'."\n";
			$year_result .= smarty_function_html_options(
				array(
					'output' => $years,
					'values' => $yearvals,
					'selected'   => $time[0],
					'print_result' => false
				),
				$smarty
			);
			$year_result .= '</select>';
		}
	}

	// Loop thru the field_order field
	for ($i = 0; $i <= 2; $i++) {
		$c = substr($field_order, $i, 1);
		switch ($c){
			case 'D':
				$html_result .= $day_result;
				break;

			case 'M':
				$html_result .= $month_result;
				break;

			case 'Y':
				$html_result .= $year_result;
				break;
		}
		// Add the field seperator
		if ($i < $field_separator_count) {
			$html_result .= $field_separator;
		}
	}

	return $html_result;
}

