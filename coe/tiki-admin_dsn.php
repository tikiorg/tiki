<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_dsn.php,v 1.15.2.1 2007-11-25 21:42:34 sylvieg Exp $
require_once ('tiki-setup.php');
include_once ('lib/admin/adminlib.php');
if ($tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('You do not have permission to use this feature'));
	$smarty->display('error.tpl');
	die;
}
if (!isset($_REQUEST["dsnId"])) {
	$_REQUEST["dsnId"] = 0;
}
$smarty->assign('dsnId', $_REQUEST["dsnId"]);
if ($_REQUEST["dsnId"]) {
	$info = $adminlib->get_dsn($_REQUEST["dsnId"]);
} else {
	$info = array();
	$info["dsn"] = '';
	$info['name'] = '';
}
$smarty->assign('info', $info);
if (isset($_REQUEST["remove"])) {
	$area = 'deldsn';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$adminlib->remove_dsn($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}
if (isset($_REQUEST["save"])) {
	check_ticket('admin-dsn');
	$adminlib->replace_dsn($_REQUEST["dsnId"], $_REQUEST["dsn"], $_REQUEST['name']);
	$info = array();
	$info["dsn"] = '';
	$info['name'] = '';
	$smarty->assign('info', $info);
	$smarty->assign('name', '');
	$smarty->assign('dsnId', '');
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'dsnId_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $adminlib->list_dsn($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('admin-dsn');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_dsn.tpl');
$smarty->display("tiki.tpl");
