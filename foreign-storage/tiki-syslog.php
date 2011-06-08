<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_permission('tiki_p_admin');

$auto_query_args = array('offset', 'numrows', 'maxRecords', 'find', 'sort_mode');
if (isset($_REQUEST["clean"])) {
	$access->check_authenticity();
	$date = strtotime("-" . $_REQUEST["months"] . " months");
	$logslib->clean_logs($date);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'lastModif_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["max"])) {
	$maxRecords = $_REQUEST["max"];
}
$smarty->assign_by_ref('maxRecords', $maxRecords);

$list = $logslib->list_logs('', '', $offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant', $list['cant']);
$smarty->assign('list', $list['data']);
$urlquery['sort_mode'] = $sort_mode;
$urlquery['find'] = $find;
$smarty->assign_by_ref('urlquery', $urlquery);
ask_ticket('admin-logs');
$smarty->assign('mid', 'tiki-syslog.tpl');
$smarty->display('tiki.tpl');
