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

// Initialization
define('COLUMN_MAXLEN', 64);

require_once('tiki-setup.php');

$access->check_feature('feature_metrics_dashboard');

$use_memcache = $memcachelib && $memcachelib->isEnabled()
                && $memcachelib->getOption('cache_metrics_output');

require_once("lib/metrics/metricslib.php");
require_once("lib/metrics/input-validation.php");
$metricslibDW = new MetricsLib($metricsdb, false);
$metricslib = new MetricsLib($dbTiki);
$tab_id = $_REQUEST['tab_id'];
$range = $_REQUEST['range'];
$date_from = $_REQUEST['date_from'];
$date_to = $_REQUEST['date_to'];
$date_field = $_REQUEST['date_field'];

if ($use_memcache) {
    $cached_output = $memcachelib->get(buildOutputCacheKey($memcachelib, $tab_id));

    if ($cached_output) {
        // If we have cached output, echo it and quit right away.
        echo $cached_output['output']; 
        echo "\n<!-- memcache ".htmlspecialchars($cache_key)."-->";
        exit;
    } else {
        // No cached output, so start buffering output for caching at the end.
        ob_start();
    }
}

$metrics_notify = '';
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
$converted_range = convert_date_range($range, $date_from, $date_to, $date_field);
if (!empty($converted_range['msg'])) {
	print tra('ERROR: ') . $converted_range['msg'];
	die;
}
$date_from = $converted_range['date_from'];
$date_to = $converted_range['date_to'];
$range_group = $converted_range['range_group'];
$timeperiod = $converted_range['timeperiod'];
if (!is_numeric($tab_id)) {
	print tra("ERROR: Invalid tab_id received. Numeric format expected, got $tab_id.");
	die;
}
//build the date range
//handle pastresults
if ($prefs['metrics_pastresults'] == 'y') {
	$date_from_past = date(DEFAULT_DATE_FORMAT, strtotime("-" . $prefs['metrics_pastresults_count'] . ' '
			. $timeperiod, strtotime($date_from)));
}
$date_range = "(`$date_field` >= '$date_from_past' AND `$date_field` <= '$date_to')";
$tab_info = $metricslib->getTabById($tab_id);
$tab_content = $tab_info['tab_content'];

//get assigned metrics
$metrics = $metricslib->getAssignedMetricsByTabId($tab_id);

$m = array();
$m_id = array();
//main part of this file, runs the SQL queries for assigned metrics
foreach ($metrics as $metric_id => $metric) {
	//skip metrics that shouldn't show up for different ranges
	if (($range == 'custom') && ($metric['metric_range_id'] == '@'))
		continue;
	if (($range == 'custom' || $range == 'monthof') && ($metric['metric_range_id'] == '-'))
		continue;
	$n = $metric['metric_name'];
	$q = $metric['metric_query'];
	if (strpos($q, '$date_range$')) {
		$q = str_replace('$date_range$', $date_range, $q);
		$range_groupby = strpos($q, 'GROUP BY') == FALSE ? 'GROUP BY ' : '';
		$range_groupby .= $range_group;
		$q = str_replace('$range_groupby$', $range_groupby, $q);
	}
	
	$temp_result = getMany($q, $metricslibDW);
	$m[$n]['result'] = $temp_result;
	$m[$n]['range'] = $metric['metric_range'];
	$m[$n]['range_id'] = $metric['metric_range_id'];
	$m[$n]['datatype'] = $metric['metric_datatype'];
	$m[$n]['datatype_id'] = $metric['metric_datatype_id'];
	$m_id[$metric_id] = $m[$n];
	$m[$n]['metric_id'] = $metric_id;
	$m_id[$metric_id]['metric_name'] = $n;
}

if (empty($m) || (count($m) > 0)) {
	$metrics_notify .= $tikilib->parse_data('{content label=MetricsEmpty}', 0);
	if (strpos($metrics_notify, '{content label=MetricsEmpty}')) $metrics_notify = '';
	print $metrics_notify;
}

//assign tab content
$smarty->assign_by_ref('m', $m);
$smarty->assign_by_ref('m_id', $m_id);
$smarty->assign_by_ref('mid', $tab_content);
$smarty->display("metrics-tab.tpl");

function getMany($query, $metricslibDW) {
	$result = $metricslibDW->query($query,array());
	$ret = array();
	while ($res = $result->fetchRow()) {
		$ret[] = $res;
	}

	return $ret;
}
function buildOutputCacheKey($memcachelib, $tab_id) {
    return $memcachelib->buildKey(array(
        'role'       => 'metrics-tab-output',
        'tab_id'     => $tab_id
    ));
}


if ($use_memcache) {
    $cached_output = array(
        'timestamp' => time(),
        'output'    => ob_get_contents()
    );
    $memcachelib->set(buildOutputCacheKey($memcachelib, $tab_id), $cached_output, false, strtotime('+1 day midnight'));
    ob_end_flush();
}
