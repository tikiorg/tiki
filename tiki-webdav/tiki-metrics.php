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
require_once('tiki-setup.php');
require_once("lib/metrics/metricslib.php");
require_once("lib/metrics/input-validation.php");

$access->check_feature('feature_metrics_dashboard');

$metricslib = new MetricsLib($dbTiki);

$tabs = $metricslib->getAllTabs();

$range = isset( $_REQUEST['range'] ) ? $_REQUEST['range'] : null;
$date_from = isset( $_REQUEST['date_from'] ) ? $_REQUEST['date_from'] : null;
$date_to = isset( $_REQUEST['date_to'] ) ? $_REQUEST['date_to'] : null;
$converted_range = convert_date_range($range, $date_from, $date_to);
if (!empty($converted_range['msg'])) {
	$smarty->assign('msg', $converted_range['msg']);
	$smarty->display('error.tpl');
	die;
}
$metrics_range = $converted_range['date_from'] . tra(' to ') . $converted_range['date_to'];
$smarty->assign('date_from', $converted_range['date_from']);
$smarty->assign('date_to', $converted_range['date_to']);
$smarty->assign('metrics_range_prefix', $converted_range['metrics_range_prefix']);
$smarty->assign('range', $range);
$smarty->assign('tabs', $tabs);
$smarty->assign('metrics_range', $metrics_range);
$smarty->assign('mid','tiki-metrics.tpl');
$smarty->display("tiki.tpl");
