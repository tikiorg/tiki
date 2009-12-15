<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/logs/logslib.php,v 1.54.2.5 2008-01-22 16:58:23 sylvieg Exp $

include_once('tiki-setup.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
if ($api_tiki != 'adodb') {
	$smarty->assign('msg', tra('This feature is disabled').': adodb');
	$smarty->display('error.tpl');
	die;
}
$query = "show tables like 'adodb_logsql'";
$result = $tikilib->query($query, array());
if (!$result->numRows()) {
	$smarty->assign('msg', tra('This feature is disabled').': log_sql');
	$smarty->display('error.tpl');
	die;
}
// let look at the log even if not active for older logs
//if ($prefs['log_sql'] != 'y') {
//	$smarty->assign('msg', tra('This feature is disabled').': log_sql');
//	$smarty->display('error.tpl');
//	die;
//}

include_once('lib/logs/logslib.php');

if (isset($_REQUEST['clean']) ) {
	$area = 'cleanlogs';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$logslib->clean_logsql();
	} else {
		key_get($area, tra('Clean the sql logs'));
	}
}
$auto_query_args = array('offset', 'numrows', 'find', 'sort_mode');

$numrows = (isset($_REQUEST['numrows'])) ? $_REQUEST['numrows']: (isset($_REQUEST['maxRecords'])? $_REQUEST['maxRecords']: $prefs['maxRecords']);
$smarty->assign_by_ref('numrows', $numrows);
$smarty->assign_by_ref('maxRecords', $numrows);
$offset = (isset($_REQUEST['offset'])) ? $_REQUEST['offset']: 0;
$smarty->assign_by_ref('offset', $offset);
$sort_mode = (isset($_REQUEST['sort_mode'])) ? $_REQUEST['sort_mode']: 'created_desc';
$smarty->assign_by_ref('sort_mode', $sort_mode);
$find = (isset($_REQUEST['find'])) ? $_REQUEST['find']: '';
$smarty->assign_by_ref('find', $find);

$logs = $logslib->list_logsql($sort_mode, $offset, $numrows, $find);
$smarty->assign_by_ref('logs', $logs['data']);
$smarty->assign_by_ref('cant', $logs['cant']);
$smarty->assign('mid', 'tiki-sqllog.tpl');
$smarty->display('tiki.tpl');
