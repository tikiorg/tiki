<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_tracker.php,v 1.8 2003-10-08 03:53:09 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once('tiki-setup.php');

include_once('lib/trackers/trackerlib.php');
include_once('lib/notifications/notificationlib.php');

if ($feature_trackers != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_trackers");

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$_REQUEST["itemId"] = 0;
$smarty->assign('itemId', $_REQUEST["itemId"]);

if (!isset($_REQUEST["trackerId"])) {
	$smarty->assign('msg', tra("No tracker indicated"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$smarty->assign('trackerId', $_REQUEST["trackerId"]);

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["trackerId"], 'tracker')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'trackers');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $_REQUEST["trackerId"], 'tracker', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
}

if ($tiki_p_view_trackers != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$tracker_info = $trklib->get_tracker($_REQUEST["trackerId"]);
$smarty->assign('tracker_info', $tracker_info);

$fields = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'fieldId_asc', '');
$ins_fields = $fields;

for ($i = 0; $i < count($fields["data"]); $i++) {
	$name = $fields["data"][$i]["name"];

	$ins_name = 'ins_' . $name;
	$fields["data"][$i]["ins_name"] = $ins_name;
	$ins_fields["data"][$i]["ins_name"] = $ins_name;

	if ($fields["data"][$i]["type"] != 'c' && $fields["data"][$i]["type"] != 'f') {
		if (isset($_REQUEST["$name"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$name"];
		} else {
			$fields["data"][$i]["value"] = '';
		}

		if (isset($_REQUEST["$ins_name"])) {
			$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_name"];
		} else {
			$ins_fields["data"][$i]["value"] = '';
		}
	}

	if ($fields["data"][$i]["type"] == 'f') {
		$fields["data"][$i]["value"] = '';

		if (isset($_REQUEST["$ins_name" . "Day"])) {
			$ins_fields["data"][$i]["value"] = mktime($_REQUEST["$ins_name" . "Hour"], $_REQUEST["$ins_name" . "Minute"],
				0, $_REQUEST["$ins_name" . "Month"], $_REQUEST["$ins_name" . "Day"], $_REQUEST["$ins_name" . "Year"]);
		} else {
			$ins_fields["data"][$i]["value"] = date("U");
		}
	}

	if ($fields["data"][$i]["type"] == 'c') {
		if (isset($_REQUEST["$name"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$name"];

			;
		} else {
			$fields["data"][$i]["value"] = '';
		}

		if (isset($_REQUEST["$ins_name"]) && $_REQUEST["$ins_name"] == 'on') {
			$ins_fields["data"][$i]["value"] = 'y';
		} else {
			$ins_fields["data"][$i]["value"] = 'n';
		}
	}
}

if ($tiki_p_admin_trackers == 'y') {
	if (isset($_REQUEST["remove"])) {
		$trklib->remove_tracker_item($_REQUEST["remove"]);
	}
}

$smarty->assign('mail_msg', '');
$smarty->assign('email_mon', '');

if ($user) {
	if (isset($_REQUEST["monitor"])) {
		$user_email = $tikilib->get_user_email($user);

		$emails = $notificationlib->get_mail_events('tracker_modified', $_REQUEST["trackerId"]);

		if (in_array($user_email, $emails)) {
			$notificationlib->remove_mail_event('tracker_modified', $_REQUEST["trackerId"], $user_email);

			$mail_msg = tra('Your email address has been removed from the list of addresses monitoring this tracker');
		} else {
			$notificationlib->add_mail_event('tracker_modified', $_REQUEST["trackerId"], $user_email);

			$mail_msg = tra('Your email address has been added to the list of addresses monitoring this tracker');
		}

		$smarty->assign('mail_msg', $mail_msg);
	}

	$user_email = $tikilib->get_user_email($user);
	$emails = $notificationlib->get_mail_events('tracker_modified', $_REQUEST["trackerId"]);

	if (in_array($user_email, $emails)) {
		$smarty->assign('email_mon', tra('Cancel monitoring'));
	} else {
		$smarty->assign('email_mon', tra('Monitor'));
	}
}

if (!isset($_REQUEST["save"])) {
	if ($_REQUEST["itemId"]) {
		$info = $trklib->get_tracker_item($_REQUEST["itemId"]);

		for ($i = 0; $i < count($fields["data"]); $i++) {
			$name = $fields["data"][$i]["name"];

			$ins_name = 'ins_' . $name;
			$ins_fields["data"][$i]["ins_name"] = $ins_name;
			$ins_fields["data"][$i]["value"] = $info["$name"];
		}
	}
}

if ($tiki_p_create_tracker_items == 'y') {
	if (isset($_REQUEST["save"])) {
		// Save here the values for this item
		$trklib->replace_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $ins_fields);

		for ($i = 0; $i < count($fields["data"]); $i++) {
			$name = $fields["data"][$i]["name"];

			$ins_name = 'ins_' . $name;
			$ins_fields["data"][$i]["ins_name"] = $ins_name;
			$ins_fields["data"][$i]["value"] = '';
		}

		$smarty->assign('itemId', '');
	}
}

$smarty->assign_by_ref('fields', $fields["data"]);
$smarty->assign_by_ref('ins_fields', $ins_fields["data"]);

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

if (!isset($_REQUEST["status"]))
	$_REQUEST["status"] = '';

$smarty->assign('status', $_REQUEST["status"]);

$items = $trklib->list_tracker_items($_REQUEST["trackerId"], $offset, $maxRecords, $sort_mode, $fields, $_REQUEST["status"]);
$cant_pages = ceil($items["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($items["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('items', $items["data"]);

$users = $userlib->get_users(0, -1, 'login_asc', '');
$groups = $userlib->get_groups(0, -1, 'groupName_asc', '');
$smarty->assign_by_ref('users', $users["data"]);
$smarty->assign_by_ref('groups', $groups["data"]);

$section = 'trackers';
include_once('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-view_tracker.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
