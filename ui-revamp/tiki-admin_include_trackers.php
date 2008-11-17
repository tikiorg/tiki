<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_trackers.php,v 1.15 2007-10-12 07:55:24 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.


//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once("lib/trackers/trackerlib.php");

if (isset($_REQUEST["trkset"])) {
	check_ticket('admin-inc-trackers');
	$tikilib->set_preference('t_use_db', $_REQUEST["t_use_db"]);
	$tikilib->set_preference('t_use_dir', $_REQUEST["t_use_dir"]);
}

// You can switch between old and new trackers
// only when no trackers exist.
if (isset($_REQUEST["trkMirrorTables"])) {

	if(isset($_REQUEST["trk_with_mirror_tables"])) {
		$_REQUEST["trk_with_mirror_tables"] = 'y';
	}
	else {
		$_REQUEST["trk_with_mirror_tables"] = 'n';
	}
		
	if(!$trklib->getOne("select * from tiki_trackers")) {
		$tikilib->set_preference('trk_with_mirror_tables', $_REQUEST["trk_with_mirror_tables"]);
	}
	elseif($prefs['trk_with_mirror_tables'] != $_REQUEST['trk_with_mirror_tables']) {
		$smarty->assign('msg', tra("You cannot mix old and new trackers"));
		$smarty->display("error.tpl");
		die;
	}
}

if (isset($_REQUEST['action']) and isset($_REQUEST['attId'])) {
	$item = $trklib->get_item_attachment($_REQUEST['attId']);
	if ($_REQUEST['action'] == 'move2db') {
		$trklib->file_to_db($prefs['t_use_dir'].$item['path'],$_REQUEST['attId']);
	} elseif ($_REQUEST['action'] == 'move2file') {
		$trklib->db_to_file($prefs['t_use_dir'] . md5($item['filename']),$_REQUEST['attId']);
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

if (isset($_REQUEST["all2db"])) {
	$attachements = $trklib->list_all_attachements();
	for ($i=0;$i<$attachements['cant'];$i++) {
		if ($attachements['data'][$i]['path']) {
			$trklib->file_to_db($prefs['t_use_dir'].$attachements['data'][$i]['path'],$attachements['data'][$i]['attId']);
		}
	}
} elseif (isset($_REQUEST["all2file"])) {
	$attachements = $trklib->list_all_attachements();
	for ($i=0;$i<$attachements['cant'];$i++) {
		if (!$attachements['data'][$i]['path']) {
			$trklib->db_to_file($prefs['t_use_dir']. md5($attachements['data'][$i]['filename']),$attachements['data'][$i]['attId']);
		}
	}
}
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
