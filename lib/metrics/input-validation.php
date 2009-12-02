<?php
/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is SUMO tools
 *
 * The Initial Developer of the Original Code is
 * Mozilla Corporation.
 * Portions created by the Initial Developer are Copyright (C) 2009
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *  Paul Craciunoiu <pcraciunoiu@mozilla.com>
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */
define('DEFAULT_DATE_FORMAT', "Y-m-d");

function convert_date_range($range, $date_from = '', $date_to = '', $date_field = '') {
	//will return this as an associative array
	$ret = array();
	$ret['msg'] = '';
	if (empty($date_from)) {
		$date_from = date(DEFAULT_DATE_FORMAT, time());
	}
	$date_from_timestamp = strtotime($date_from);
	$date_from_parts = explode('-', $date_from);
	foreach ($date_from_parts as $k => $date_int) {
		$date_from_parts[$k] = intval($date_int);
	}
	$now = time();
	if (!$date_from_timestamp) {
		$ret['msg'] = tra('Invalid "from" date received:').' '.$date_from;
		return $ret;
	}
	elseif ($date_from_timestamp > $now) {
		$date_now = date(DEFAULT_DATE_FORMAT, $now);
		$ret['msg'] = tra("Sorry, no data available for the future:"). "$date_from > $date_now";
		return $ret;
	}
	elseif (
		($date_from_parts[0] < 1900) || ($date_from_parts[1] < 1) || ($date_from_parts[1] > 12)
		|| ($date_from_parts[2] < 1) || ($date_from_parts[2] > 31)
		) {
		$ret['msg'] = tra('Invalid "from" date received:').' '.$date_from;
		return $ret;
	}
	else {
		$date_from = date(DEFAULT_DATE_FORMAT, $date_from_timestamp);
	}

	switch($range) {
		case 'monthof':
			$date_from = date('Y-m-01', $date_from_timestamp);
			$date_to_timestamp = strtotime('-1 day', strtotime('+1 month', $date_from_timestamp));
			$date_to = date(DEFAULT_DATE_FORMAT, $date_to_timestamp);
			$metrics_range_prefix = tra("for month of");
			$timeperiod = "months";
			$range_group = "MONTH(`$date_field`)";
			break;
		case 'custom':
			// we only care about date_to for custom ranges
			if (empty($date_to)) {
				$date_to = date(DEFAULT_DATE_FORMAT, strtotime($date_from));
			}
			$date_to_timestamp = strtotime($date_to);
			if (!$date_to_timestamp) {
				$ret['msg'] = tra('Invalid "to" date received:').' '.$date_to;
				return $ret;
			}
			else {
				$date_to = date(DEFAULT_DATE_FORMAT, $date_to_timestamp);
			}
			$metrics_range_prefix = tra("for");
			$timeperiod = "days";
			$range_group = 'date_field';
			break;
		case 'weekof':
		case 'lastweek':
		default:
			if ($range == 'lastweek') {
				$date_from_timestamp = time();
			}
			// need to add one day to avoid going back 2 weeks on "last Monday"
			$date_from_timestamp = strtotime('+ 1 day', $date_from_timestamp);
			$date_from_timestamp = strtotime("last Monday", $date_from_timestamp);
			$date_from = date(DEFAULT_DATE_FORMAT, $date_from_timestamp);
			$date_to_timestamp = strtotime("next Sunday", $date_from_timestamp);
			$date_to = date(DEFAULT_DATE_FORMAT, $date_to_timestamp);
			$metrics_range_prefix = tra("for week of");
			$timeperiod = "weeks";
			$range_group = "WEEK(`$date_field`,1)";
	}
	if ($date_from_timestamp > $date_to_timestamp) {
		$ret['msg'] = tra('Sorry, "from" date must precede "to" date:') . " $date_from > $date_to";
		return $ret;
	}

	$ret['date_from'] = $date_from;
	$ret['date_from_timestamp'] = $date_from_timestamp;
	$ret['date_to'] = $date_to;
	$ret['date_to_timestamp'] = $date_to_timestamp;
	$ret['date_from'] = $date_from;
	$ret['metrics_range_prefix'] = $metrics_range_prefix;
	$ret['timeperiod'] = $timeperiod;
	if ($date_field) {
		$ret['range_group'] = $range_group;
	}
	return $ret;
} // end of convert_date_range
