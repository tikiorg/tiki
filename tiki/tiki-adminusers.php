<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-adminusers.php,v 1.14 2004-01-14 01:13:29 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once('lib/modules/modlib.php');
include_once ('lib/userprefs/scrambleEmail.php');
include_once ('lib/userprefs/userprefslib.php');

if ($user != 'admin') {
	if ($tiki_p_admin != 'y') {
		$smarty->assign('msg', tra("You dont have permission to use this feature"));
		$smarty->display("error.tpl");
		die;
	}
}

function discardUser($u, $reason) {
	$u['reason'] = $reason;
	return $u;
}

function batchImportUsers() {
	global $userlib, $smarty;

	$fname = $_FILES['csvlist']['tmp_name'];
	$fhandle = fopen($fname, "r");
	$fields = fgetcsv($fhandle, 1000);
	if (!$fields[0]) {
		$smarty->assign('msg', tra("The file is not a CSV file or has not a correct syntax"));
		$smarty->display("error.tpl");
		die;
	}
	while (!feof($fhandle)) {
		$data = fgetcsv($fhandle, 1000);
		for ($i = 0; $i < count($fields); $i++) {
			@$ar[$fields[$i]] = $data[$i];
		}
		$userrecs[] = $ar;
	}
	fclose ($fhandle);
	if (!is_array($userrecs)) {
		$smarty->assign('msg', tra("No records were found. Check the file please!"));
		$smarty->display("error.tpl");
		die;
	}
	$added = 0;
	foreach ($userrecs as $u) {
		if (empty($u['login'])) {
			$discarded[] = discardUser($u, tra("User login is required"));
		} elseif (empty($u['password'])) {
			$discarded[] = discardUser($u, tra("Password is required"));
		} elseif (empty($u['email'])) {
			$discarded[] = discardUser($u, tra("Email is required"));
		} elseif ($userlib->user_exists($u['login'])and (!$_REQUEST['overwrite'])) {
			$discarded[] = discardUser($u, tra("User is duplicated"));
		} else {
			if (!$userlib->user_exists($u['login'])) {
				$userlib->add_user($u['login'], $u['password'], $u['email']);
			}

			$userlib->set_user_fields($u);

			if (@$u['groups']) {
				$grps = explode(",", $u['groups']);

				foreach ($grps as $grp) {
					if ($userlib->group_exists($grp)) {
						$userlib->assign_user_to_group($u['login'], $grp);
					}
				}
			}
			$added++;
		}
	}
	$smarty->assign('added', $added);
	if (@is_array($discarded)) {
		$smarty->assign('discarded', count($discarded));
	}
	@$smarty->assign('discardlist', $discarded);
}

// Process the form to add a user here
if (isset($_REQUEST["newuser"])) {
	check_ticket('admin-users');
	// if no user data entered, check if it's a batch upload  
	if ((!$_REQUEST["name"]) and (is_uploaded_file($_FILES['csvlist']['tmp_name']))) {
		batchImportUsers();
	} else {
		// Check if the user already exists
		if ($_REQUEST["pass"] != $_REQUEST["pass2"]) {
			$smarty->assign('msg', tra("The passwords dont match"));
			$smarty->display("error.tpl");
			die;
		} else {
			if ($userlib->user_exists($_REQUEST["name"])) {
				$smarty->assign('msg', tra("User already exists"));
				$smarty->display("error.tpl");
				die;
			} else {
				$userlib->add_user($_REQUEST["name"], $_REQUEST["pass"], $_REQUEST["email"]);
			}
		}
	}
}


if (isset($_REQUEST["action"])) {
	check_ticket('admin-users');
	if ($_REQUEST["action"] == 'delete') {
		$userlib->remove_user($_REQUEST["user"]);
	}
	if ($_REQUEST["action"] == 'removegroup') {
		$userlib->remove_user_from_group($_REQUEST["user"], $_REQUEST["group"]);
	}
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'login_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST["numrows"])) {
	$numrows = $maxRecords;
} else {
	$numrows = $_REQUEST["numrows"];
}
$smarty->assign_by_ref('numrows', $numrows);

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

$users = $userlib->get_users($offset, $numrows, $sort_mode, $find, $initial);
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

$smarty->assign_by_ref('users', $users["data"]);
ask_ticket('admin-users');

$smarty->assign('uses_tabs', 'y');

$smarty->assign('mid', 'tiki-adminusers.tpl');
$smarty->display("tiki.tpl");
?>
