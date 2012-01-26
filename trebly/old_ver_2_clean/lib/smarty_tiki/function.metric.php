<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/** Constants used in function metric */
define('COLUMN_MAXLEN', 64);
/**
 * Smarty function for getting a metric. Syntax:
 * {metric 
 * [sparkline|trend|table|toggle][=true]
 * [name="name from metrics_metric"
 * id="id from metrics_metric"] (one of name and id required)
 * numrows = # of data pts to go back (max # of row from SQL query)
 * value = "name of field in SQL query to display"
 * date_field = "date column in SQL to get dates from"
 * html_before (optional) = "HTMl before table"
 * html_after (optional) = "HTML after table"
 * }
 * @param string name (required if id not specified) metric name to get
 * @param integer id (required if name not specified) metric id to get
 * @param string title (optional) title to display, defaults to name
 * @param integer numrows (optional) number of data points to show in the sparkline
 * 		also number of rows to show in the table, defaults to all if not specified
 * @param string value name of field in SQL query to display
 * @param string date_field (optional) name of field in SQL query to get dates from
 * 		defaults to "date_field"
 * @param string html_before (optional) HTMl before table
 * @param string html_after (optional) HTML after table
 *
 * @param boolean sparkline defaults to false, specify "sparkline" 
 * 		or "sparkline=true" to enable
 * @param boolean trend defaults to true, specify "sparkline" 
 * 		or "sparkline=false" to disable
 * @param boolean table defaults to true, specify "table" 
 * 		or "table=false" to disable
 * @param boolean toggle defaults to false, specify "toggle" 
 * 		or "toggle=true" to enable
 * @return HTML formatted for metric
 */
function smarty_function_metric($params, &$smarty)
{
    global $m, $m_id, $prefs;
    extract($params);
    // Validate input first
    if (empty($name) && empty($id)) {
    	print '<div class="metricbox">ERROR: Metric name or id required, but not specified.</div>';
    	return;
    }
    if (empty($name)) {
    	$name = $m_id[$id]['metric_name'];
    }
    if (empty($id)) {
    	$id = $m[$name]['metric_id'];
    }
    if (!isset($m[$name])) { 
    	return;
	}
    if (empty($value)) {
    	print '<div class="metricbox">ERROR: Value column required, but not specified.</div>';
    	return;
    }
    if (empty($numrows) || !is_numeric($numrows)) {
    	$numrows = count($m[$name]['result']);
    	if ($numrows <= 0) {
    		print '<div class="metricbox">Metric ' . $name . ' has no results to show.</div>';
    		return;
    	}
    }
    if (empty($title)) {
    	$title = $name;
    }
    else {
    	/** 
    	 * inspired by drupal 6 check_plain 
    	 * at http://api.drupal.org/api/function/check_plain/6
    	 */
		if (preg_match('/^./us', $title) == 1) {
    		$title = htmlspecialchars($title, ENT_QUOTES);
    	}
    	else {
    		$title = $name;
    	}
    }
	if (empty($date_field)) {
		$date_field = 'date_field';
	}
	else {
    	/** 
    	 * strip ` and limit column length
    	 */
		$date_field = preg_replace('/[`\/\\\<>"\']/','', $date_field);
		$date_field = substr($date_field, 0, COLUMN_MAXLEN);
	}
    if ($sparkline !== TRUE) {
    	$sparkline = FALSE;
    }
    if ($trend !== TRUE) {
    	$trend = FALSE;
    }
    if ($table !== TRUE) {
    	$table = FALSE;
    }
    if ($toggle !== TRUE) {
    	$toggle = FALSE;
    }
    if (!empty($html_before)) {
    	$html_before = htmlentities($html_before, ENT_NOQUOTES, 'UTF-8', FALSE);
    }
    else {
    	$html_before = '';
    }
    if (!empty($html_after)) {
    	$html_after = htmlentities($html_after, ENT_NOQUOTES, 'UTF-8', FALSE);
    }
    else {
    	$html_after = '';
    }
    // end of validate input
    
    $out = ' <div class="metricbox"><div class="title">' . "\n";
    if ($sparkline == TRUE) {
    	$out .= ' <span class="inlinesparkline">' . "\n";
    	$first = TRUE;
    	for ($i = $numrows; $i--; $i > 0) {
    		if (!$first) $out .= ',';
    		$first = FALSE;
    		$out .= $m[$name]['result'][$i][$value];
    	}
    	$out .= '</span> ';
    }
    if ($toggle == TRUE) {
    	$out .= ' <a class="toggle-button">Toggle</a> ' . "\n";
    }
    $out .= ' <span class="metrictitle">' . $title . "</span> ";
    $out .= metric_helper_number_format($m[$name]['result'][0][$value], $m[$name]['datatype_id']) . "\n";
    if ($trend == TRUE) {
    	if (count($m[$name]['result']) < 2) {
    		$out .= ' <span class="trend">' . $prefs['metrics_trend_novalue'] . '</span> ';
    	}
    	else {
			$trend_val = $m[$name]['result'][0][$value] / $m[$name]['result'][1][$value] * 100 - 100;
			if ($trend_val > 0) {
				$class = "green";
			}
			elseif ($trend_val == 0) {
				$class = "gray";
			}
			else {
				$class = "red";
			}
			$out .= ' <span class="trend trend-' . $class . '">' . $prefs['metrics_trend_prefix'] . number_format(round($trend_val, 2), 2) . $prefs['metrics_trend_suffix'] . '</span> ';
		}
    }
    $out .= '</div> ' . "\n"; //end of metric title
    if ($toggle == TRUE) {
    	$out .= ' <div class="toggle">' . "\n";
    }
    $out .= $html_before;
    if ($table == TRUE) {
    	$out .= ' <table class="trend-table">' . "\n";
    	$out .= ' <tr><th>';
    	switch($_REQUEST['range']) {
    		case 'monthof':
    			$out .= tra('Month beginning');
    			break;
			case 'weekof':
			case 'lastweek':
				$out .= tra('Week beginning');
				break;
			case 'custom':
			default:
				$out .= tra('Day');
    	}
    	$out .= '</th><th>' . tra('Data (change %)') . '</th></tr> ' . "\n";
    	for ($i = 0; $i < $numrows; $i++) {
    		if ($i == $numrows-1) {
    			$trend_val = ' ' . $prefs['metrics_trend_novalue'];
    		}
    		else {
    			$trend_val = $m[$name]['result'][$i][$value] / $m[$name]['result'][$i+1][$value] * 100 - 100;
				if ($trend_val > 0) {
					$class = "green";
				}
				elseif ($trend_val == 0) {
					$class = "gray";
				}
				else {
					$class = "red";
				}
				$trend_val = ' <span class="trend trend-' . $class . '">' . $prefs['metrics_trend_prefix']
					 . number_format(round($trend_val, 2), 2) . $prefs['metrics_trend_suffix'] . '</span> ';
			}
    		$out .= ' <tr><td>' . $m[$name]['result'][$i][$date_field] . '</td><td>' . metric_helper_number_format($m[$name]['result'][$i][$value], $m[$name]['datatype_id']) . $trend_val . '</td></tr> ';
    	}
    	$out .= '</table> ';
    }
    $out .= $html_after;
    if ($toggle == TRUE) {
    	$out .= '</div> ' . "\n"; //end of <div class="toggle">
    }
	$out .= '</div> ' . "\n"; //end of <div class="metricbox">
	print $out;
}

function metric_helper_number_format($numval, $type) {
	switch ($type) {
		case 'i':
			return number_format(round($numval));
		case 'f':
			return number_format($numval, 2);
		case '%':
			return number_format($numval * 100, 2) . '%';
		default:
			return $numval;
	}
}
