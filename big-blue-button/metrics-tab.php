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

require_once("lib/metrics/metricslib.php");
require_once("lib/metrics/input-validation.php");
$metricslib = new MetricsLib;

$tab_id = $_REQUEST['tab_id'];
$range = $_REQUEST['range'];
$date_from = $_REQUEST['date_from'];
$date_to = $_REQUEST['date_to'];
$date_field = $_REQUEST['date_field'];

require_once 'lib/cache/pagecache.php';
$pageCache = Tiki_PageCache::create()
	->requiresPreference( 'metrics_cache_output' )
	->addKeys( $_REQUEST, array( 'tab_id', 'range', 'date_from', 'date_to', 'date_field' ) )
	->applyCache();

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
if (!is_numeric($tab_id)) {
	print tra("ERROR: Invalid tab_id received. Numeric format expected, got $tab_id.");
	die;
}

$converted_range = convert_date_range($range, $date_from, $date_to, $date_field);
if (!empty($converted_range['msg'])) {
	print tra('ERROR: ') . $converted_range['msg'];
	die;
}

$tab_info = $metricslib->getTabById($tab_id);
$ret = $metricslib->getMetricsData( $tab_info, $range, $converted_range, $date_field );
$tab_content = $tab_info['tab_content'];
$m = $ret['data'];
$m_id = $ret['ids'];

//assign tab content
$smarty->assign_by_ref('m', $m);
$smarty->assign_by_ref('m_id', $m_id);
$smarty->assign_by_ref('mid', $tab_content);
$smarty->display("metrics-tab.tpl");

