<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_quicktags.php,v 1.6 2004-04-16 05:53:35 franck Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/quicktags/quicktagslib.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["tagId"])) {
	$_REQUEST["tagId"] = 0;
}

$smarty->assign('tagId', $_REQUEST["tagId"]);

if ($_REQUEST["tagId"]) {
	$info = $quicktagslib->get_quicktag($_REQUEST["tagId"]);
} else {
	$info = array();

	$info["taglabel"] = '';
	$info['taginsert'] = '';
	$info['tagicon'] = '';
}


if (isset($_REQUEST["remove"])) {
	$area = "delquicktag";
	if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
		key_check($area);
		$quicktagslib->remove_quicktag($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["save"])) {
	$quicktagslib->replace_quicktag($_REQUEST["tagId"], $_REQUEST["taglabel"], $_REQUEST['taginsert'], $_REQUEST['tagicon'],$_REQUEST['tagcategory']);

	$info = array();
	$info["taglabel"] = '';
	$info['taginsert'] = '';
	$info['tagicon'] = '';
	$info['tagcategory'] = '';
	$smarty->assign('name', '');
}

$smarty->assign('info', $info);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'tagId_desc';
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

if (isset($_REQUEST["category"])) {
	$category = $_REQUEST["category"];
} else {
	$category = '';
}

$smarty->assign('category', $category);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$quicktags = $quicktagslib->list_quicktags($offset, $maxRecords, $sort_mode, $find, $category);

$cant_pages = ceil($quicktags["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($quicktags["cant"] > ($offset + $maxRecords)) {
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
$icon_path = array("images","img/icons","img/icn");
$list_icons = $quicktagslib->list_icons($icon_path);
$smarty->assign_by_ref('list_icons', $list_icons);

$smarty->assign_by_ref('quicktags', $quicktags["data"]);

// Display the template
$smarty->assign('mid', 'tiki-admin_quicktags.tpl');
$smarty->display("tiki.tpl");

?>
