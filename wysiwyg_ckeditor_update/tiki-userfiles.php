<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'mytiki';
require_once ('tiki-setup.php');
if ($prefs['ajax_xajax'] == "y") {
	require_once ('lib/ajax/ajaxlib.php');
}
include_once ('lib/userfiles/userfileslib.php');

$access->check_feature('feature_userfiles', '', 'community');
$access->check_user($user);
$access->check_permission('tiki_p_userfiles');

$quota = $userfileslib->userfiles_quota($user);
$limit = $prefs['userfiles_quota'] * 1024 * 1000;
if ($limit == 0) $limit = 999999999;
$percentage = ($quota / $limit) * 100;
$cellsize = round($percentage / 100 * 200);
$percentage = round($percentage);
$smarty->assign('cellsize', $cellsize);
$smarty->assign('percentage', $percentage);
$smarty->assign('limitmb', $prefs['userfiles_quota']);
// Process upload here
for ($i = 0; $i < 5; $i++) {
	if (isset($_FILES["userfile$i"]) && is_uploaded_file($_FILES["userfile$i"]['tmp_name'])) {
		check_ticket('user-files');
		$fp = fopen($_FILES["userfile$i"]['tmp_name'], "rb");
		$data = '';
		$fhash = '';
		$name = $_FILES["userfile$i"]['name'];
		if ($prefs['uf_use_db'] == 'n') {
			$fhash = md5(uniqid('.'));
			$fw = fopen($prefs['uf_use_dir'] . $fhash, "wb");
			if (!$fw) {
				$smarty->assign('msg', tra('Cannot write to this file:') . $fhash);
				$smarty->display("error.tpl");
				die;
			}
		}
		while (!feof($fp)) {
			if ($prefs['uf_use_db'] == 'y') {
				$data.= fread($fp, 8192 * 16);
			} else {
				$data = fread($fp, 8192 * 16);
				fwrite($fw, $data);
			}
		}
		fclose($fp);
		if ($prefs['uf_use_db'] == 'n') {
			fclose($fw);
			$data = '';
		}
		$size = $_FILES["userfile$i"]['size'];
		$name = $_FILES["userfile$i"]['name'];
		$type = $_FILES["userfile$i"]['type'];
		if ($quota + $size > $limit) {
			$smarty->assign('msg', tra('Cannot upload this file not enough quota'));
			$smarty->display("error.tpl");
			die;
		}
		$userfileslib->upload_userfile($user, '', $name, $type, $size, $data, $fhash);
	}
}
// Process removal here
if (isset($_REQUEST["delete"]) && isset($_REQUEST["userfile"])) {
	check_ticket('user-files');
	foreach(array_keys($_REQUEST["userfile"]) as $file) {
		$userfileslib->remove_userfile($user, $file);
	}
}
$quota = $userfileslib->userfiles_quota($user);
$limit = $prefs['userfiles_quota'] * 1024 * 1000;
if ($limit == 0) $limit = 999999999;
$percentage = $quota / $limit * 100;
$cellsize = round($percentage / 100 * 200);
$percentage = round($percentage);
if ($cellsize == 0) $cellsize = 1;
$smarty->assign('cellsize', $cellsize);
$smarty->assign('percentage', $percentage);
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
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
if (isset($_SESSION['thedate'])) {
	$pdate = $_SESSION['thedate'];
} else {
	$pdate = $tikilib->now;
}
$channels = $userfileslib->list_userfiles($user, $offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
include_once ('tiki-mytiki_shared.php');
ask_ticket('user-files');
if ($prefs['ajax_xajax'] == "y") {
	function user_files_ajax() {
		global $ajaxlib, $xajax;
		$ajaxlib->registerTemplate("tiki-userfiles.tpl");
		$ajaxlib->registerTemplate("tiki-my_tiki.tpl");
		$ajaxlib->registerFunction("loadComponent");
		$ajaxlib->processRequests();
	}
	user_files_ajax();
}
$smarty->assign('mid', 'tiki-userfiles.tpl');
$smarty->display("tiki.tpl");
