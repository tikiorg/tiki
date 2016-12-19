<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'mytiki';
require_once ('tiki-setup.php');
include_once ('lib/notepad/notepadlib.php');
include_once ('lib/userfiles/userfileslib.php');
$access->check_feature('feature_notepad');
$access->check_user($user);
$access->check_permission('tiki_p_notepad');
// Process upload here
if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
	check_ticket('notepad-list');
	$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
	$data = '';
	while (!feof($fp)) {
		$data.= fread($fp, 8192 * 16);
	}
	fclose($fp);
	if (strlen($data) > 1000000) {
		$smarty->assign('msg', tra("The file is too large"));
		$smarty->display("error.tpl");
		die;
	}
	$size = $_FILES['userfile1']['size'];
	$name = $_FILES['userfile1']['name'];
	$type = $_FILES['userfile1']['type'];
	$notepadlib->replace_note($user, 0, $name, $data);
}
if (isset($_REQUEST["merge"])) {
	check_ticket('notepad-list');
	$merge = '';
	$first = true;
	if (!isset($_REQUEST["note"])) {
		$smarty->assign('msg', tra("No item indicated"));
		$smarty->display("error.tpl");
		die;
	}
	foreach (array_keys($_REQUEST["note"]) as $note) {
		$data_c = $notepadlib->get_note($user, $note);
		$data = $data_c['data'];
		if ($first) {
			$first = false;
			$merge.= "---------" . tra('merged note:') . $data_c['name'] . "----" . "\n";
			$merge.= $data;
		} else {
			$merge.= "\n---------" . tra('merged note:') . $data_c['name'] . "----" . "\n";
			$merge.= $data;
		}
	}
	// Now create the merged note
	$tikilib->replace_note($user, 0, $_REQUEST['merge_name'], $merge);
}
if (isset($_REQUEST["delete"]) && isset($_REQUEST["note"])) {
	foreach (array_keys($_REQUEST["note"]) as $note) {
		$notepadlib->remove_note($user, $note);
	}
}
$quota = $userfileslib->userfiles_quota($user);
$limit = $prefs['userfiles_quota'] * 1024 * 1000;
if ($limit == 0) $limit = 999999999;
$percentage = ($quota / $limit) * 100;
$cellsize = round($percentage / 100 * 200);
if ($cellsize == 0) $cellsize = 1;
$percentage = round($percentage);
$smarty->assign('cellsize', $cellsize);
$smarty->assign('percentage', $percentage);
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'lastModif_desc';
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
$channels = $notepadlib->list_notes($user, $offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
include_once ('tiki-section_options.php');
include_once ('tiki-mytiki_shared.php');
ask_ticket('notepad-list');
$smarty->assign('mid', 'tiki-notepad_list.tpl');
$smarty->display("tiki.tpl");
