<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admingroups.php,v 1.11 2003-11-17 15:44:28 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

// PERMISSIONS: NEEDS p_admin
if ($user != 'admin') {
	if ($tiki_p_admin != 'y') {
		$smarty->assign('msg', tra("You dont have permission to use this feature"));

		$smarty->display("error.tpl");
		die;
	}
}

$ag_home = '';
if (isset($_REQUEST["home"])) $ag_home = $_REQUEST["home"];

// Process the form to add a group
if (isset($_REQUEST["newgroup"])) {
	// Check if the user already exists
	if ($userlib->group_exists($_REQUEST["name"])) {
		$smarty->assign('msg', tra("Group already exists"));

		$smarty->display("error.tpl");
		die;
	} else {
		$userlib->add_group(addslashes($_REQUEST["name"]),addslashes($_REQUEST["desc"]),$ag_home);
		if (isset($_REQUEST["include_groups"])) {
			foreach ($_REQUEST["include_groups"] as $include) {
				if ($_REQUEST["name"] != $include) {
					$userlib->group_inclusion(addslashes($_REQUEST["name"]), $include);
				}
			}
		}
	}

	$_REQUEST["group"] = $_REQUEST["name"];
}

// modification
if (isset($_REQUEST["save"])and isset($_REQUEST["olgroup"])) {
	$userlib->change_group(addslashes($_REQUEST["olgroup"]),addslashes($_REQUEST["name"]),addslashes($_REQUEST["desc"]),$ag_home);

	$userlib->remove_all_inclusions($_REQUEST["name"]);

	if (isset($_REQUEST["include_groups"])) {
		foreach ($_REQUEST["include_groups"] as $include) {
			if ($_REQUEST["name"] != $include) {
				$userlib->group_inclusion(addslashes($_REQUEST["name"]), $include);
			}
		}
	}
	if (isset($_REQUEST['batch_set_default']) and $_REQUEST['batch_set_default'] == 'on') {
		$userlib->batch_set_default_group($_REQUEST["name"]);
	}
	$_REQUEST["group"] = $_REQUEST["name"];
}

// Process a form to remove a group
if (isset($_REQUEST["action"])) {
	if ($_REQUEST["action"] == 'delete') {
		$userlib->remove_group($_REQUEST["group"]);
	}

	if ($_REQUEST["action"] == 'remove') {
		$userlib->remove_permission_from_group($_REQUEST["permission"], $_REQUEST["group"]);
	}
}

// Sort options and pagination for the group list
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'groupName_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
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

$users = $userlib->get_groups($offset, $maxRecords, $sort_mode, $find);

$inc = array();
list($groupname,$groupdesc,$grouphome,$groupperms) = array('','','','');

if (isset($_REQUEST["group"])and $_REQUEST["group"]) {
	$re = $userlib->get_group_info($_REQUEST["group"]);

	if (isset($re["groupName"]))
		$groupname = $re["groupName"];

	if (isset($re["groupDesc"]))
		$groupdesc = $re["groupDesc"];

	if(isset($re["groupHome"]))
		$grouphome = $re["groupHome"];

	$groupperms = $re["perms"];
	$rs = $userlib->get_included_groups($_REQUEST["group"]);

	foreach ($users["data"] as $r) {
		$rr = $r["groupName"];

		$inc["$rr"] = "n";

		if (in_array($rr, $rs)) {
			$inc["$rr"] = "y";
		}
	}
} else {
	$_REQUEST["group"] = 0;
}

$smarty->assign('inc', $inc);
$smarty->assign('group', $_REQUEST["group"]);
$smarty->assign('groupname', $groupname);
$smarty->assign('groupdesc', $groupdesc);
$smarty->assign('grouphome',$grouphome);
$smarty->assign('groupperms', $groupperms);

$cant_pages = ceil($users["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($users["cant"] > ($offset + $maxRecords)) {
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

// Assign the list of groups
$smarty->assign_by_ref('users', $users["data"]);
// Display the template for group administration
$smarty->assign('mid', 'tiki-admingroups.tpl');
$smarty->display("tiki.tpl");

?>