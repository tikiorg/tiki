<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('../tiki-setup.php');

if ($prefs['feature_tikitests'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': feature_tikitests');
	$smarty->display('error.tpl');
	die;
}

if ($tiki_p_admin_tikitests != 'y' and $tiki_p_edit_tikitests != 'y') {
	$smarty->assign('msg', tra('You do not have permission to do that'));
	$smarty->display('error.tpl');
	die;
}

$smarty->assign('tidy', extension_loaded('tidy'));
$smarty->assign('http', extension_loaded('http'));
$smarty->assign('curl', extension_loaded('curl'));

if (isset($_POST['action']) and isset($_POST['filename']) and trim($_POST['filename']) != '') {
	setcookie('tikitest_record', '1', 0, '/');
	setcookie('tikitest_filename', trim($_POST['filename']), 0, '/');
	if (isset($_REQUEST['current_session'])) {
		header('Location: ../tiki-index.php');
	} else {
		header('Location: ../tiki-logout.php');
	}
	die();
}
$smarty->assign('mid', 'tiki-tests_record.tpl');
$smarty->assign('title', tra('TikiTest Record'));
$smarty->display('tiki.tpl');
