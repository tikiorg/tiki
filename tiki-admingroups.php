<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admingroups.php,v 1.30 2004-03-02 16:00:27 mose Exp $

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

list($trackers,$ag_utracker,$ag_ufield,$ag_gtracker,$ag_gfield) = array(array(),0,0,0,0);

if (isset($groupTracker) and $groupTracker == 'y') {
	$trackerlist = $tikilib->list_trackers(0, -1, 'name_asc', '');
	$trackers = $trackerlist['list'];
	if (isset($_REQUEST["groupstracker"]) and isset($trackers[$_REQUEST["groupstracker"]])) {
		$ag_gtracker = $_REQUEST["groupstracker"];
		if (isset($_REQUEST["groupfield"]) and $_REQUEST["groupfield"]) {
			$ag_gfield = $_REQUEST["groupfield"];
		}
	}
}

if (isset($userTracker) and $userTracker == 'y') {
	if (!isset($trackerlist)) $trackerlist = $tikilib->list_trackers(0, -1, 'name_asc', '');
	$trackers = $trackerlist['list'];
	if (isset($_REQUEST["userstracker"]) and isset($trackers[$_REQUEST["userstracker"]])) {
		$ag_utracker = $_REQUEST["userstracker"];
		if (isset($_REQUEST["usersfield"]) and $_REQUEST["usersfield"]) {
			$ag_ufield = $_REQUEST["usersfield"];
		}
	}
}
$smarty->assign('trackers', $trackers);

$ag_home = '';
if (isset($_REQUEST["home"])) $ag_home = $_REQUEST["home"];

// Process the form to add a group
if (isset($_REQUEST["newgroup"])) {
	check_ticket('admin-groups');
	// Check if the user already exists
	if ($userlib->group_exists($_REQUEST["name"])) {
		$smarty->assign('msg', tra("Group already exists"));
		$smarty->display("error.tpl");
		die;
	} else {
		$userlib->add_group($_REQUEST["name"],$_REQUEST["desc"],$ag_home,$ag_utracker,$ag_gtracker);
		if (isset($_REQUEST["include_groups"])) {
			foreach ($_REQUEST["include_groups"] as $include) {
				if ($_REQUEST["name"] != $include) {
					$userlib->group_inclusion($_REQUEST["name"], $include);
				}
			}
		}
	}
	$_REQUEST["group"] = $_REQUEST["name"];
}

// modification
if (isset($_REQUEST["save"]) and isset($_REQUEST["olgroup"])) {
	check_ticket('admin-groups');
	$userlib->change_group($_REQUEST["olgroup"],$_REQUEST["name"],$_REQUEST["desc"],$ag_home,$ag_utracker,$ag_gtracker,$ag_ufield,$ag_gfield);
	$userlib->remove_all_inclusions($_REQUEST["name"]);
	if (isset($_REQUEST["include_groups"]) and is_array($_REQUEST["include_groups"])) {
		foreach ($_REQUEST["include_groups"] as $include) {
			if ($_REQUEST["name"] != $include) {
				$userlib->group_inclusion($_REQUEST["name"], $include);
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
	check_ticket('admin-groups');
	if ($_REQUEST["action"] == 'delete') {
		$userlib->remove_group($_REQUEST["group"]);
	}
	if ($_REQUEST["action"] == 'remove') {
		$userlib->remove_permission_from_group($_REQUEST["permission"], $_REQUEST["group"]);
	}
}

if (!isset($_REQUEST["numrows"])) {
	$numrows = $maxRecords;
} else {
	$numrows = $_REQUEST["numrows"];
}
$smarty->assign_by_ref('numrows', $numrows);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'groupName_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["initial"])) {
	$initial = $_REQUEST["initial"];
} else {
	$initial = '';
}
$smarty->assign('initial', $initial);
$smarty->assign('initials', split(' ','a b c d e f g h i j k l m n o p q r s t u v w x y z'));

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);


$users = $userlib->get_groups($offset, $numrows, $sort_mode, $find, $initial);

$inc = array();
list($groupname,$groupdesc,$grouphome,$userstrackerid,$usersfieldid,$grouptrackerid,$groupfieldid,$groupperms,$trackerinfo,$memberlist) = array('','','','','','','','','','');

if (isset($_REQUEST["group"])and $_REQUEST["group"]) {
	$re = $userlib->get_group_info($_REQUEST["group"]);

	if (isset($re["groupName"]))
		$groupname = $re["groupName"];

	if (isset($re["groupDesc"]))
		$groupdesc = $re["groupDesc"];

	if(isset($re["groupHome"]))
		$grouphome = $re["groupHome"];

	if ($userTracker == 'y') {
		if (isset($re["usersTrackerId"]) and $re["usersTrackerId"]) {
			$userstrackerid = $re["usersTrackerId"];
		}
		if (isset($re["usersFieldId"]) and $re["usersFieldId"]) {
			$usersfieldid = $re["usersFieldId"];
		}
	}

	if ($groupTracker == 'y') {	
		if (isset($re["groupTrackerId"]) and $re["groupTrackerId"])  {
			include_once('lib/trackers/trackerlib.php');
			$grouptrackerid = $re["groupTrackerId"];
			$fields = $trklib->list_tracker_fields($grouptrackerid, 0, -1, 'position_asc', '');
			$gfields = $fields;
			if (isset($re["groupFieldId"]) and $re["groupFieldId"])  {
				$groupfieldid = $re["groupFieldId"];
				$info = $trklib->get_item($grouptrackerid,$groupfieldid,$groupname);
				$groupitemId = $info["itemId"];
				$smarty->assign('groupitemId', $groupitemId);
				for ($i = 0; $i < count($fields["data"]); $i++) {
					if ($fields["data"][$i]["isPublic"] == 'y' or $tiki_p_admin) {
						$name = $fields["data"][$i]["fieldId"];
						if ($fields["data"][$i]["type"] != 'h') {
							if ($fields["data"][$i]["type"] == 'c') {
								if (!isset($info["$name"])) $info["$name"] = 'n';
							} else {
								if (!isset($info["$name"])) $info["$name"] = '';
							}
							if ($fields["data"][$i]["type"] == 'e') {
								include_once('lib/categories/categlib.php');
								$k = $fields["data"][$i]["options"];
								$fields["data"][$i]["$k"] = $categlib->get_child_categories($k);
								if (!isset($cat)) {
									$cat = $categlib->get_object_categories("tracker ".$grouptrackerid,$groupitemId);
								}
								foreach ($cat as $c) {
									$fields["data"][$i]["cat"]["$c"] = 'y';
								}
							} elseif  ($fields["data"][$i]["type"] == 'r') {
								$fields["data"][$i]["linkId"] = $trklib->get_item_id($fields["data"][$i]["options_array"][0],$fields["data"][$i]["options_array"][1],$info["$name"]);
								$fields["data"][$i]["value"] = $info["$name"];
								$fields["data"][$i]["type"] = 't';
							} elseif ($fields["data"][$i]["type"] == 'a') {
								$fields["data"][$i]["value"] = $info["$name"];
								$fields["data"][$i]["pvalue"] = $tikilib->parse_data($info["$name"]);
							} else {
								$fields["data"][$i]["value"] = $info["$name"];
							}
						}
					}
				}
			}
			$smarty->assign_by_ref('fields', $fields["data"]);
		}
		$groupFields = array();
		if ($grouptrackerid) {
			$groupFields = $gfields;
		}
		$smarty->assign_by_ref('groupFields', $groupFields['data']);
	}

	if ($userTracker == 'y') {
		$usersFields = array();
		if ($userstrackerid) {
			include_once('lib/trackers/trackerlib.php');
			$usersFields = $trklib->list_tracker_fields($userstrackerid, 0, -1, 'position_asc', '');
		}
		$smarty->assign_by_ref('usersFields', $usersFields['data']);
	}

	$groupperms = $re["perms"];
	
	$allgroups = $userlib->list_all_groups();
	$rs = $userlib->get_included_groups($_REQUEST["group"]);

	foreach ($allgroups as $rr) {
		$inc["$rr"] = "n";
		if (in_array($rr, $rs)) {
			$inc["$rr"] = "y";
		}
	}

	setcookie("activeTabs".urlencode(substr($_SERVER["REQUEST_URI"],1,80)),"tab2");
} else {
	$allgroups = $userlib->list_all_groups();
	foreach ($allgroups as $rr) {
		$inc["$rr"] = "n";
	}
	setcookie("activeTabs".urlencode(substr($_SERVER["REQUEST_URI"],1,80)),"tab1");
	$_REQUEST["group"] = 0;
}
if (isset($_REQUEST['add'])) {
	setcookie("activeTabs".urlencode(substr($_SERVER["REQUEST_URI"],1,80)),"tab2");
}

if ($_REQUEST['group']) {
	$memberslist = $userlib->get_group_users($_REQUEST['group']);
} else {
	$memberslist = '';
}
$smarty->assign('memberslist',$memberslist);

$smarty->assign('inc', $inc);
$smarty->assign('group', $_REQUEST["group"]);
$smarty->assign('groupname', $groupname);
$smarty->assign('groupdesc', $groupdesc);
$smarty->assign('grouphome',$grouphome);
if (isset($groupTracker) and $groupTracker == 'y') {
	$smarty->assign('grouptrackerid',$grouptrackerid);
	$smarty->assign('groupfieldid',$groupfieldid);
}
if (isset($userTracker) and $userTracker == 'y') {
	$smarty->assign('userstrackerid',$userstrackerid);
	$smarty->assign('usersfieldid',$usersfieldid);
}
$smarty->assign('groupperms', $groupperms);

$cant_pages = ceil($users["cant"] / $numrows);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $numrows));

if ($users["cant"] > ($offset + $numrows)) {
	$smarty->assign('next_offset', $offset + $numrows);
} else {
	$smarty->assign('next_offset', -1);
}
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $numrows);
} else {
	$smarty->assign('prev_offset', -1);
}
ask_ticket('admin-groups');

$smarty->assign('uses_tabs', 'y');

// Assign the list of groups
$smarty->assign_by_ref('users', $users["data"]);
// Display the template for group administration
$smarty->assign('mid', 'tiki-admingroups.tpl');
$smarty->display("tiki.tpl");

?>
