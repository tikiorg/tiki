<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-eph_admin.php,v 1.8 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/ephemerides/ephlib.php');
include_once ("lib/class_calendar.php");

if ($feature_eph != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_eph");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_eph_admin != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_SESSION['thedate'])) {
	$pdate = $_SESSION['thedate'];
} else {
	$pdate = date("U");
}

if (!isset($_REQUEST['day']))
	$_REQUEST['day'] = date("d");

if (!isset($_REQUEST['mon']))
	$_REQUEST['mon'] = date("m");

if (!isset($_REQUEST['year']))
	$_REQUEST['year'] = date("Y");

$smarty->assign('day', $_REQUEST['day']);
$smarty->assign('mon', $_REQUEST['mon']);
$smarty->assign('year', $_REQUEST['year']);
//$pdate=date("U",strtotime($_REQUEST['year']."-".$_REQUEST['mon']."-".$_REQUEST['day'])+86399);
$pdate = mktime(23, 59, 59, $_REQUEST['mon'], $_REQUEST['day'], $_REQUEST['year']);

if (!isset($_REQUEST['ephId']))
	$_REQUEST['ephId'] = 0;

$smarty->assign('ephId', $_REQUEST['ephId']);

if (!$_REQUEST['ephId']) {
	$info = array();

	$info['title'] = '';
	$info['textdata'] = '';
	$info['publish'] = date("U");
} else {
	$info = $ephlib->get_eph($_REQUEST['ephId']);

	$pdate = $info["publish"];
}

$smarty->assign('pdate', $pdate);
$smarty->assign('info', $info);

if (isset($_REQUEST['save'])) {
	check_ticket('admin-eph');
	// Process upload here
	$data = '';

	//$date = $tikilib->make_server_time(0,0,0,$_REQUEST["Date_Month"],$_REQUEST["Date_Day"],$_REQUEST["Date_Year"],$tikilib->get_display_timezone($user));
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

		$data = '';

		while (!feof($fp)) {
			$data .= fread($fp, 8192 * 16);
		}

		fclose ($fp);
		$size = $_FILES['userfile1']['size'];
		$name = $_FILES['userfile1']['name'];
		$type = $_FILES['userfile1']['type'];
		$ephlib->replace_eph($_REQUEST['ephId'], $_REQUEST['title'], $name, $type, $size, $data, $pdate, $_REQUEST['textdata']);
	} else {
		$size = 0;

		$ephlib->replace_eph($_REQUEST['ephId'], $_REQUEST['title'], '', '', 0, $data, $pdate, $_REQUEST['textdata']);
	}

	$info = array();
	$info['title'] = '';
	$info['textdata'] = '';
	$info['publish'] = date("U");
	$smarty->assign('info', $info);
	$smarty->assign('ephId', 0);
}

// Process removal here
if (isset($_REQUEST["delete"])) {
	check_ticket('admin-eph');
	foreach (array_keys($_REQUEST["ephitem"])as $item) {
		$ephlib->remove_eph($item);
	}
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'title_desc';
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
$channels = $ephlib->list_eph($offset, $maxRecords, $sort_mode, $find, $pdate);
$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);

$smarty->assign('tasks_useDates', $tasks_useDates);
ask_ticket('admin-eph');

$smarty->assign('mid', 'tiki-eph_admin.tpl');
$smarty->display("tiki.tpl");

?>
