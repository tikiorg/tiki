<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_permission( 'tiki_p_admin' );
$access->check_feature( 'feature_metrics_dashboard' );

require_once 'lib/admin/adminlib.php';
require_once("lib/metrics/metricslib.php");
$metricslib = new MetricsLib($dbTiki);
$metric_range_all = $metricslib->getMetricsRangeAll();
$smarty->assign('metric_range_all', $metric_range_all);
$metric_datatype_all = $metricslib->getMetricsDatatypeAll();
$smarty->assign('metric_datatype_all', $metric_datatype_all);
$headerlib->add_cssfile("css/metrics.css");

$dsn_list = $adminlib->list_dsn( 0, -1, 'name_asc', '' );
$smarty->assign( 'dsn_list', $dsn_list['data'] );

/* Edit or delete a metric */
if (isset($_REQUEST["metric_submit"])) {
	if (empty($_REQUEST["metric_name"])) {
	    $smarty->assign('msg',tra("Cannot create or update metric: You need to specify a name for the metric."));
	    $smarty->display("error.tpl");
	    die;
	}
	if (strlen($_REQUEST["metric_name"]) > $prefs['metrics_metric_name_length']) {
	    $smarty->assign('msg',tr("Cannot create or update metric: Metric name must be under %0 characters in length.", $prefs['metrics_metric_name_length']));
	    $smarty->display("error.tpl");
	    die;	
	}
	if (empty($_REQUEST["metric_range"])) {
	    $smarty->assign('msg',tra("Cannot create or update metric: You need to specify a range for the metric."));
	    $smarty->display("error.tpl");
	    die;
	}
	if (empty($_REQUEST["metric_datatype"])) {
	    $smarty->assign('msg',tra("Cannot create or update metric: You need to specify a data type for the metric."));
	    $smarty->display("error.tpl");
	    die;
	}
	if (empty($_REQUEST["metric_query"])) {
	    $smarty->assign('msg',tra("Cannot create or update metric: You need to specify a query for the metric."));
	    $smarty->display("error.tpl");
	    die;
	}
	
	check_ticket('admin-metrics');
	
	$smarty->assign('metric_name', $_REQUEST["metric_name"]);
	$smarty->assign('metric_range', $_REQUEST["metric_range"]);
	$smarty->assign('metric_datatype', $_REQUEST["metric_datatype"]);
	$smarty->assign('metric_query', $_REQUEST["metric_query"]);
	$smarty->assign('metric_dsn', $_REQUEST["metric_dsn"]);
	if (empty($_POST['metric_id']) || (!is_numeric($_POST['metric_id']))) {
		//create
		$metric_id = NULL;
		$logslib->add_log('adminmetrics','created metric '.$_REQUEST["metric_name"]);
	}
	else {
		//update
		$metric_id = $_POST['metric_id'];
		$logslib->add_log('adminmetrics','updated metric '.$_REQUEST["metric_name"]);
	}
	$smarty->assign('metric_id', $metric_id);
	$metricslib->createUpdateMetric($metric_id, $_REQUEST["metric_name"], $_REQUEST["metric_range"], $_REQUEST["metric_datatype"], $_REQUEST["metric_query"], $_REQUEST['metric_dsn']);
}

/* Edit or delete a tab */
if (isset($_REQUEST["tab_submit"])) {
	if (empty($_REQUEST["tab_name"])) {
	    $smarty->assign('msg',tra("Cannot create or update tab: You need to specify a name for the tab."));
	    $smarty->display("error.tpl");
	    die;
	}
	if (strlen($_REQUEST["tab_name"]) > $prefs['metrics_tab_name_length']) {
	    $smarty->assign('msg',tr("Cannot create or update tab: Tab name must be under %0 characters in length.", $prefs['metrics_tab_name_length']));
	    $smarty->display("error.tpl");
	    die;	
	}
	if (empty($_REQUEST["tab_order"]) || (!is_numeric($_REQUEST['tab_order']))) {
	    $smarty->assign('msg',tra("Cannot create or update tab: You need to specify an integer range for the tab order."));
	    $smarty->display("error.tpl");
	    die;
	}
	
	if (empty($_REQUEST["tab_content"])) {
	    $smarty->assign('msg',tra("Cannot create or update tab: Tab content cannot be empty."));
	    $smarty->display("error.tpl");
	    die;
	}
	check_ticket('admin-metrics');
	
	$smarty->assign('tab_name', $_REQUEST["tab_name"]);
	$smarty->assign('tab_order', $_REQUEST["tab_order"]);
	$smarty->assign('tab_content', $_REQUEST["tab_content"]);
	if (empty($_POST['tab_id']) || (!is_numeric($_POST['tab_id']))) {
		//create
		$tab_id = NULL;
		$logslib->add_log('adminmetrics','created tab '.$_REQUEST["tab_name"]);
	}
	else {
		//update
		$tab_id = $_POST['tab_id'];
		$logslib->add_log('adminmetrics','updated tab '.$_REQUEST["tab_name"]);
	}
	$smarty->assign('tab_id', $tab_id);
	$metricslib->createUpdateTab($tab_id, $_REQUEST["tab_name"], $_REQUEST["tab_order"], $_REQUEST["tab_content"]);
}

/* Remove a metric */
if (isset($_REQUEST["metric_remove"])) {
	check_ticket('admin-metrics');
	if (!is_numeric($_REQUEST["metric_remove"])) {
		$smarty->assign('msg', tra('metric_remove must be a numeric value'));
		$smarty->display('error.tpl');
		die;
	}
	$metricslib->removeMetricById($_REQUEST["metric_remove"]);
	$logslib->add_log('adminmetrics','removed metric '.$_REQUEST["metric_remove"]);
}

/* Remove a tab */
if (isset($_REQUEST["tab_remove"])) {
	check_ticket('admin-metrics');
	if (!is_numeric($_REQUEST["tab_remove"])) {
		$smarty->assign('msg', tra('tab_remove must be a numeric value'));
		$smarty->display('error.tpl');
		die;
	}
	$metricslib->removeTabById($_REQUEST["tab_remove"]);
	$logslib->add_log('adminmetrics','removed tab '.$_REQUEST["tab_remove"]);
}

/* Remove an assignment */
if (isset($_REQUEST["assign_remove"])) {
	check_ticket('admin-metrics');
	if (!is_numeric($_REQUEST["assign_remove"])) {
		$smarty->assign('msg', tra('assign_remove must be a numeric value'));
		$smarty->display('error.tpl');
		die;
	}
	$metricslib->removeMetricAssignedById($_REQUEST["assign_remove"]);
	$logslib->add_log('adminmetrics','unassigned '.$_REQUEST["tab_remove"]);
}

/* Edit a metric */
if (isset($_REQUEST["metric_edit"]) || $_POST["metric_id"]) {
	check_ticket('admin-metrics');
	$metric_id = $_REQUEST["metric_edit"];
	if (!is_numeric($metric_id)) {
		$metric_id = $_POST['metric_id'];
	}
	if (!is_numeric($metric_id)) {
		$smarty->assign('msg', tra('metric_edit must be a numeric value'));
		$smarty->display('error.tpl');
		die;
	}
	$metric_info = $metricslib->getMetricById($metric_id);
	$smarty->assign('metric_id', $metric_info["metric_id"]);
	$smarty->assign('metric_name', $metric_info["metric_name"]);
	$smarty->assign('metric_range', $metric_info["metric_range"]);
	$smarty->assign('metric_datatype', $metric_info["metric_datatype"]);
	$smarty->assign('metric_dsn', $metric_info["metric_dsn"]);
	$smarty->assign('metric_query', $metric_info["metric_query"]);
}

/* Edit a tab */
if (isset($_REQUEST["tab_edit"]) || $_POST["tab_id"]) {
	check_ticket('admin-metrics');
	$tab_id = $_REQUEST["tab_edit"];
	if (!is_numeric($tab_id)) {
		$tab_id = $_POST['tab_id'];
	}
	if (!is_numeric($tab_id)) {
		$smarty->assign('msg', tra('tab_edit must be a numeric value'));
		$smarty->display('error.tpl');
		die;
	}
	$tab_info = $metricslib->getTabById($tab_id);
	$smarty->assign('tab_id', $tab_info["tab_id"]);
	$smarty->assign('tab_name', $tab_info["tab_name"]);
	$smarty->assign('tab_order', $tab_info["tab_order"]);
	$smarty->assign('tab_content', $tab_info["tab_content"]);
}

/* Clear cache for a tab */
$use_memcache = $memcachelib && $memcachelib->isEnabled()
    && $memcachelib->getOption('cache_metrics_output');
$smarty->assign('use_memcache', $use_memcache);
if (isset($_REQUEST["tab_clearcache"])) {
    if (!$use_memcache) {
        $smarty->assign('msg', tra('Memcache is disabled for metrics.'));
        $smarty->display('error.tpl');
        die;
    }
    check_ticket('admin-metrics');
    if (!is_numeric($_REQUEST["tab_clearcache"])) {
        $smarty->assign('msg', tra('tab_clearcache must be a numeric value'));
        $smarty->display('error.tpl');
        die;
    }
    $memcachelib->delete($memcachelib->buildKey(array(
        'role'       => 'metrics-tab-output',
        'tab_id'     => $_REQUEST["tab_clearcache"]
    )));
    $logslib->add_log('adminmetrics','cleared cache for tab '.$_REQUEST["tab_clearcache"]);
}

/* Assign a metric to a tab */
if (!empty($_REQUEST['assign_metric_new'])) {
	check_ticket('admin-metrics');
	if (!is_numeric($_REQUEST['assign_metric_new'])) {
		$smarty->assign('msg', tra('assign_metric must be a numeric value'));
		$smarty->display('error.tpl');
		die;
	}
	$smarty->assign('assign_metric', $_REQUEST['assign_metric_new']);
}

if (isset($_REQUEST["assign_metric_edit"])) {
	check_ticket('admin-metrics');
	if (!is_numeric($_REQUEST["assign_metric_edit"])) {
		$smarty->assign('msg', tra('assign_metric_edit must be a numeric value'));
		$smarty->display('error.tpl');
		die;
	}
	$assign_info = $metricslib->getMetricAssignedById($_REQUEST["assign_metric_edit"]);
	$smarty->assign('assigned_id', $assign_info["assigned_id"]);
	$smarty->assign('assign_metric', $assign_info["metric_id"]);
	$smarty->assign('assign_tab', $assign_info["tab_id"]);
}
if (!empty($_REQUEST['assign_metric_new'])) {
	check_ticket('admin-metrics');
	if (!is_numeric($_REQUEST['assign_metric_new'])) {
		$smarty->assign('msg', tra('assign_metric must be a numeric value'));
		$smarty->display('error.tpl');
		die;
	}
	$smarty->assign('assign_metric', $_REQUEST['assign_metric_new']);
}

if (isset($_REQUEST["assign"])) {
	check_ticket('admin-metrics');
	if (!is_numeric($_REQUEST["assign_metric"])) {
		$smarty->assign('msg', tra('Assign: Invalid metric value'));
		$smarty->display('error.tpl');
		die;
	}
	if (!is_numeric($_REQUEST["assign_tab"])) {
		$smarty->assign('msg', tra('Assign: Invalid tab value'));
		$smarty->display('error.tpl');
		die;
	}
	if (empty($_POST['assigned_id']) || (!is_numeric($_POST['assigned_id']))) {
		//create
		$assigned_id = NULL;
		$logslib->add_log('adminmodules','assigned new metric '.$_REQUEST["assign_metric"]);
	}
	else {
		//update
		$assigned_id = $_POST['assigned_id'];
		$logslib->add_log('adminmodules','reassigned metric '.$_REQUEST["assign_metric"] . ' (assigned_id = ' .$assigned_id .')');
	}
	$smarty->assign('assigned_id', $assigned_id);

	$smarty->assign('assign_metric', $_REQUEST["assign_metric"]);
	$smarty->assign('assign_tab', $_REQUEST["assign_tab"]);
	$metricslib->createUpdateMetricAssigned($assigned_id, $_REQUEST["assign_metric"], $_REQUEST["assign_tab"]);
	header ("location: tiki-admin_metrics.php");
}

$metrics_list = $metricslib->getAllMetrics();
$smarty->assign('metrics_list', $metrics_list);
$tabs_list = $metricslib->getAllTabs();
$smarty->assign('tabs_list', $tabs_list);
$metrics_assigned_list = $metricslib->getAllMetricsAssigned();
$smarty->assign('metrics_assigned_list', $metrics_assigned_list);

ask_ticket('admin-metrics');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-admin_metrics.tpl');
$smarty->display("tiki.tpl");

