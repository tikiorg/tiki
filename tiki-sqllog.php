<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' =>
		array(
			'clean' => 'striptags',
			'offset' => 'digits',
			'numrows' => 'digits',
			'maxRecords' => 'digits',
			'find' => 'striptags',
			'sort_mode' => 'striptags',
		)
	)
);

include_once ('tiki-setup.php');

$access->check_permission('tiki_p_admin');

if ($api_tiki != 'adodb') {
	$smarty->assign('msg', tra('This feature is disabled') . ': adodb');
	$smarty->display('error.tpl');
	die;
}

$query = "show tables like 'adodb_logsql'";
$result = $tikilib->query($query, array());
if (!$result->numRows()) {
	$smarty->assign('msg', tra('This feature is disabled') . ': log_sql');
	$smarty->display('error.tpl');
	die;
}
// let look at the log even if not active for older logs
//if ($prefs['log_sql'] != 'y') {
//	$smarty->assign('msg', tra('This feature is disabled').': log_sql');
//	$smarty->display('error.tpl');
//	die;
//}
if (isset($_REQUEST['clean'])) {
	$access->check_authenticity(tra('Clean the sql logs'));
	$logslib->clean_logsql();
}
$auto_query_args = array('offset', 'numrows', 'find', 'sort_mode');
$numrows = (isset($_REQUEST['numrows'])) ? $_REQUEST['numrows'] : (isset($_REQUEST['maxRecords']) ? $_REQUEST['maxRecords'] : $prefs['maxRecords']);
$smarty->assign_by_ref('numrows', $numrows);
$smarty->assign_by_ref('maxRecords', $numrows);
$offset = (isset($_REQUEST['offset'])) ? $_REQUEST['offset'] : 0;
$smarty->assign_by_ref('offset', $offset);
$sort_mode = (isset($_REQUEST['sort_mode'])) ? $_REQUEST['sort_mode'] : 'created_desc';
$smarty->assign_by_ref('sort_mode', $sort_mode);
$find = (isset($_REQUEST['find'])) ? $_REQUEST['find'] : '';
$smarty->assign_by_ref('find', $find);
$logs = $logslib->list_logsql($sort_mode, $offset, $numrows, $find);
$smarty->assign_by_ref('logs', $logs['data']);
$smarty->assign_by_ref('cant', $logs['cant']);
$smarty->assign('mid', 'tiki-sqllog.tpl');
$smarty->display('tiki.tpl');
