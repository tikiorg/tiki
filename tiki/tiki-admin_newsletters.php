<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_newsletters.php,v 1.7 2003-12-28 20:12:51 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/newsletters/nllib.php');

if ($feature_newsletters != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsletters");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["nlId"])) {
	$_REQUEST["nlId"] = 0;
}

$smarty->assign('nlId', $_REQUEST["nlId"]);

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["nlId"], 'newsletter')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'newsletters');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $_REQUEST["nlId"], 'newsletter', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
}

if ($tiki_p_admin_newsletters != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($_REQUEST["nlId"]) {
	$info = $nllib->get_newsletter($_REQUEST["nlId"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["description"] = '';
	$info["allowUserSub"] = 'y';
	$info["allowAnySub"] = 'n';
	$info["unsubMsg"] = 'y';
	$info["validateAddr"] = 'y';
}

$smarty->assign('info', $info);

if (isset($_REQUEST["remove"])) {
	check_ticket('admin-nl');
	$nllib->remove_newsletter($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-nl');
	if (isset($_REQUEST["allowUserSub"]) && $_REQUEST["allowUserSub"] == 'on') {
		$_REQUEST["allowUserSub"] = 'y';
	} else {
		$_REQUEST["allowUserSub"] = 'n';
	}

	if (isset($_REQUEST["allowAnySub"]) && $_REQUEST["allowAnySub"] == 'on') {
		$_REQUEST["allowAnySub"] = 'y';
	} else {
		$_REQUEST["allowAnySub"] = 'n';
	}

	if (isset($_REQUEST["unsubMsg"]) && $_REQUEST["unsubMsg"] == 'on') {
		$_REQUEST["unsubMsg"] = 'y';
	} else {
		$_REQUEST["unsubMsg"] = 'n';
	}

	if (isset($_REQUEST["validateAddr"]) && $_REQUEST["validateAddr"] == 'on') {
		$_REQUEST["validateAddr"] = 'y';
	} else {
		$_REQUEST["validateAddr"] = 'n';
	}

	$sid = $nllib->replace_newsletter($_REQUEST["nlId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["allowUserSub"], $_REQUEST["allowAnySub"], $_REQUEST["unsubMsg"], $_REQUEST["validateAddr"]);
	/*
	$cat_type='newsletter';
	$cat_objid = $sid;
	$cat_desc = substr($_REQUEST["description"],0,200);
	$cat_name = $_REQUEST["name"];
	$cat_href="tiki-newsletters.php?nlId=".$cat_objid;
	include_once("categorize.php");
	*/
	$info["name"] = '';
	$info["description"] = '';
	$info["allowUserSub"] = 'y';
	$info["allowAnySub"] = 'n';
	$info["unsubMsg"] = 'y';
	$info["validateAddr"] = 'y';
	//$info["frequency"] = 7 * 24 * 60 * 60;
	$smarty->assign('nlId', 0);
	$smarty->assign('info', $info);
}

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
$channels = $nllib->list_newsletters($offset, $maxRecords, $sort_mode, $find);

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

// Fill array with possible number of questions per page
/*
$freqs = array();

for ($i = 0; $i < 90; $i++) {
	$aux["i"] = $i;

	$aux["t"] = $i * 24 * 60 * 60;
	$freqs[] = $aux;
}

$smarty->assign('freqs', $freqs);
*/
/*
$cat_type='newsletter';
$cat_objid = $_REQUEST["nlId"];
include_once("categorize_list.php");
*/
ask_ticket('admin-nl');

// Display the template
$smarty->assign('mid', 'tiki-admin_newsletters.tpl');
$smarty->display("tiki.tpl");

?>
