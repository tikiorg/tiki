<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-syslog.php,v 1.2 2004-03-31 10:07:56 mose Exp $

require_once ('tiki-setup.php');

if ($tiki_p_admin != 'y') {
	if (!$user) {
		$smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
		$smarty->assign('errortitle',tra("Please login"));
	} else {
		$smarty->assign('msg', tra("You dont have permission to use this feature"));
	}
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'logtime_desc';
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

$list = $logslib->list_logs('','',$offset, $maxRecords, $sort_mode, $find);
$smarty->assign('list', $list['data']);

$urlquery['sort_mode'] = $sort_mode;
$urlquery['find'] = $find;
$smarty->assign_by_ref('urlquery', $urlquery);
$cant = $list['cant'];
include "tiki-pagination.php";

$smarty->assign('mid', 'tiki-syslog.tpl');
$smarty->display('tiki.tpl');
?>
