<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_menus.php,v 1.10 2004-03-31 07:38:41 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/menubuilder/menulib.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["menuId"])) {
	$_REQUEST["menuId"] = 0;
}

$smarty->assign('menuId', $_REQUEST["menuId"]);

if ($_REQUEST["menuId"]) {
	$info = $tikilib->get_menu($_REQUEST["menuId"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["description"] = '';
	$info["type"] = 'd';
}

$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('type', $info["type"]);

if (isset($_REQUEST["remove"])) {
  $area = 'delmenu';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
    key_check($area);
		$menulib->remove_menu($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-menus');
	$menulib->replace_menu($_REQUEST["menuId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["type"]);

	$smarty->assign('name', '');
	$smarty->assign('description', '');
	$smarty->assign('type', '');
	$_REQUEST["menuId"] = 0;
	$smarty->assign('menuId', 0);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_desc';
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
$channels = $menulib->list_menus($offset, $maxRecords, $sort_mode, $find);

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
ask_ticket('admin-menus');

// Display the template
$smarty->assign('mid', 'tiki-admin_menus.tpl');
$smarty->display("tiki.tpl");

?>
