<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_tracker_item.php,v 1.13 2003-12-02 07:42:31 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/trackers/trackerlib.php');
include_once ('lib/notifications/notificationlib.php');

if ($feature_trackers != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["itemId"])) {
	$smarty->assign('msg', tra("No item indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('itemId', $_REQUEST["itemId"]);
$item_info = $trklib->get_tracker_item($_REQUEST["itemId"]);
$smarty->assign('item_info', $item_info);

if (!isset($_REQUEST["trackerId"])) {
	$smarty->assign('msg', tra("No tracker indicated"));

	$smarty->display("error.tpl");
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

	$smarty->display("error.tpl");
	die;
}

$tracker_info = $trklib->get_tracker($_REQUEST["trackerId"]);
$smarty->assign('tracker_info', $tracker_info);

$fields = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'fieldId_asc', '');
$ins_fields = $fields;

for ($i = 0; $i < count($fields["data"]); $i++) {
	$name = $fields["data"][$i]["name"];

	$ins_name = 'ins_' . $name;
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

if ($tiki_p_modify_tracker_items == 'y') {
	if (isset($_REQUEST["save"])) {
		// Save here the values for this item
		$trklib->replace_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $ins_fields, $_REQUEST["status"]);

		for ($i = 0; $i < count($fields["data"]); $i++) {
			$name = $fields["data"][$i]["name"];

			$ins_name = 'ins_' . $name;
			$ins_fields["data"][$i]["value"] = '';
		}

		$item_info = $trklib->get_tracker_item($_REQUEST["itemId"]);
		$smarty->assign('item_info', $item_info);
	}
}

if ($_REQUEST["itemId"]) {
	$info = $trklib->get_tracker_item($_REQUEST["itemId"]);

	for ($i = 0; $i < count($fields["data"]); $i++) {
		$name = $fields["data"][$i]["name"];

		$ins_name = 'ins_' . $name;
		$ins_fields["data"][$i]["value"] = $info["$name"];
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

/*
$items=$trklib->list_tracker_items($trackerId,$offset,$maxRecords,$sort_mode,$fields);
$cant_pages = ceil($items["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($items["cant"] > ($offset+$maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}

$smarty->assign_by_ref('items',$items["data"]);
*/
$users = $userlib->get_users_names(0, -1, 'login_asc', '');
$smarty->assign_by_ref('users', $users);
$groups = $userlib->get_groups(0, -1, 'groupName_asc', '');
$smarty->assign_by_ref('groups', $groups["data"]);

$smarty->assign('mail_msg', '');
$smarty->assign('email_mon', '');

if ($user) {
	if (isset($_REQUEST["monitor"])) {
		$user_email = $tikilib->get_user_email($user);

		$emails = $notificationlib->get_mail_events('tracker_item_modified', $_REQUEST["itemId"]);

		if (in_array($user_email, $emails)) {
			$notificationlib->remove_mail_event('tracker_item_modified', $_REQUEST["itemId"], $user_email);

			$mail_msg = tra('Your email address has been removed from the list of addresses monitoring this item');
		} else {
			$notificationlib->add_mail_event('tracker_item_modified', $_REQUEST["itemId"], $user_email);

			$mail_msg = tra('Your email address has been added to the list of addresses monitoring this item');
		}

		$smarty->assign('mail_msg', $mail_msg);
	}

	$user_email = $tikilib->get_user_email($user);
	$emails = $notificationlib->get_mail_events('tracker_item_modified', $_REQUEST["itemId"]);

	if (in_array($user_email, $emails)) {
		$smarty->assign('email_mon', tra('Cancel monitoring'));
	} else {
		$smarty->assign('email_mon', tra('Monitor'));
	}
}

if ($tracker_info["useComments"] == 'y') {
	if ($tiki_p_admin_trackers == 'y') {
		if (isset($_REQUEST["remove_comment"])) {
			$trklib->remove_item_comment($_REQUEST["remove_comment"]);
		}
	}

	if (isset($_REQUEST["commentId"])) {
		$comment_info = $trklib->get_item_comment($_REQUEST["commentId"]);

		$smarty->assign('comment_title', $comment_info["title"]);
		$smarty->assign('comment_data', $comment_info["data"]);
	} else {
		$_REQUEST["commentId"] = 0;

		$smarty->assign('comment_title', '');
		$smarty->assign('comment_data', '');
	}

	$smarty->assign('commentId', $_REQUEST["commentId"]);

	if ($_REQUEST["commentId"] && $tiki_p_admin_trackers != 'y') {
		$_REQUEST["commentId"] = 0;
	}

	if ($tiki_p_comment_tracker_items == 'y') {
		if (isset($_REQUEST["save_comment"])) {
			$trklib->replace_item_comment($_REQUEST["commentId"], $_REQUEST["itemId"], $_REQUEST["comment_title"],
				$_REQUEST["comment_data"], $user);

			$smarty->assign('comment_title', '');
			$smarty->assign('comment_data', '');
			$smarty->assign('commentId', 0);
		}
	}

	$comments = $trklib->list_item_comments($_REQUEST["itemId"], 0, -1, 'posted_desc', '');
	$smarty->assign_by_ref('comments', $comments["data"]);
}

if ($tracker_info["useAttachments"] == 'y') {
	if (isset($_REQUEST["removeattach"])) {
		$owner = $trklib->get_item_attachment_owner($_REQUEST["removeattach"]);

		if (($user && ($owner == $user)) || ($tiki_p_wiki_admin_attachments == 'y')) {
			$trklib->remove_item_attachment($_REQUEST["removeattach"]);
		}
	}

	if (isset($_REQUEST["attach"]) && ($tiki_p_attach_trackers == 'y')) {
		// Process an attachment here
		if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
			$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

			$data = '';
			$fhash = '';

			if ($t_use_db == 'n') {
				$fhash = md5($name = $_FILES['userfile1']['name']);

				$fw = fopen($t_use_dir . $fhash, "wb");

				if (!$fw) {
					$smarty->assign('msg', tra('Cannot write to this file:'). $fhash);

					$smarty->display("error.tpl");
					die;
				}
			}

			while (!feof($fp)) {
				if ($t_use_db == 'y') {
					$data .= fread($fp, 8192 * 16);
				} else {
					$data = fread($fp, 8192 * 16);

					fwrite($fw, $data);
				}
			}

			fclose ($fp);

			if ($t_use_db == 'n') {
				fclose ($fw);

				$data = '';
			}

			$size = $_FILES['userfile1']['size'];
			$name = $_FILES['userfile1']['name'];
			$type = $_FILES['userfile1']['type'];
			$trklib->item_attach_file($_REQUEST["itemId"], $name, $type, $size, $data, $_REQUEST["attach_comment"], $user, $fhash);
		}
	}

	$atts = $trklib->list_item_attachments($_REQUEST["itemId"], 0, -1, 'created_desc', '');
	$smarty->assign('atts', $atts["data"]);
}

$section = 'trackers';
include_once ('tiki-section_options.php');

$smarty->assign('uses_tabs', 'y');
// Display the template
$smarty->assign('mid', 'tiki-view_tracker_item.tpl');
$smarty->display("tiki.tpl");

?>
