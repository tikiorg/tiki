<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_trackers.php,v 1.5 2004-03-08 02:10:44 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

include "lib/trackers/trackerlib.php";

if (isset($_REQUEST["trkset"])) {
	check_ticket('admin-inc-trackers');
	$tikilib->set_preference('t_use_db', $_REQUEST["t_use_db"]);
	$tikilib->set_preference('t_use_dir', $_REQUEST["t_use_dir"]);
	$smarty->assign('t_use_db', $_REQUEST["t_use_db"]);
	$smarty->assign('t_use_dir', $_REQUEST["t_use_dir"]);
}

if (isset($_REQUEST['action']) and isset($_REQUEST['attId'])) {
	$item = $trklib->get_item_attachment($_REQUEST['attId']);
	if ($_REQUEST['action'] == 'move2db') {
		$trklib->file_to_db($t_use_dir.$item['path'],$_REQUEST['attId']);
	} elseif ($_REQUEST['action'] == 'move2file') {
		$trklib->db_to_file($t_use_dir . md5($item['filename']),$_REQUEST['attId']);
	}
}

if (!isset($_REQUEST["find"])) {
	$find = '';
} else {
	$find = $_REQUEST["find"];
}
$smarty->assign_by_ref('find', $find);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);

if (!isset($_REQUEST["sort_mode"])) {
		$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);


$attachements = $trklib->list_all_attachements($offset,$maxRecords,$sort_mode,$find);
$smarty->assign_by_ref('attachements', $attachements['data']);
$urlquery['find'] = $find;
$urlquery['page'] = 'trackers';
$urlquery['sort_mode'] = $sort_mode;
$smarty->assign_by_ref('urlquery', $urlquery);
$cant = $attachements['cant'];
include "tiki-pagination.php";
ask_ticket('admin-inc-trackers');
?>
