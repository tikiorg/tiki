<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_tracker_item.php,v 1.45 2004-02-10 09:43:14 mose Exp $

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

$tracker_info = $trklib->get_tracker($_REQUEST["trackerId"]);
$tracker_info = array_merge($tracker_info,$trklib->get_tracker_options($_REQUEST["trackerId"]));
$smarty->assign('tracker_info', $tracker_info);

if ($tiki_p_view_trackers != 'y' and ($tracker_info["writerCanModify"] != 'y') and ($tracker_info["writerGroupCanModify"] != 'y')) {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$status_types = $trklib->status_types();
$smarty->assign('status_types', $status_types);

$fields = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'position_asc', '');
$ins_fields = $fields;

$usecategs = false;
$ins_categs = array();
$textarea_options = false;

for ($i = 0; $i < count($fields["data"]); $i++) {
	$fid = $fields["data"][$i]["fieldId"];
	
	$ins_id = 'ins_' . $fid;
	$fields["data"][$i]["ins_id"] = $ins_id;
	
	$filter_id = 'filter_' . $fid;
	$fields["data"][$i]["filter_id"] = $filter_id;

	if (!isset($mainfield) and $fields["data"][$i]['isMain'] == 'y') {
		$mainfield = $ins_fields["data"][$i]["name"];
	}

	if ($fields["data"][$i]["type"] == 'f') {
		$fields["data"][$i]["value"] = '';
		$ins_fields["data"][$i]["value"] = '';
		if (isset($_REQUEST["$ins_id" . "Day"])) {
			$ins_fields["data"][$i]["value"] = mktime($_REQUEST["$ins_id" . "Hour"], $_REQUEST["$ins_id" . "Minute"],
			0, $_REQUEST["$ins_id" . "Month"], $_REQUEST["$ins_id" . "Day"], $_REQUEST["$ins_id" . "Year"]);
		} else {
			$ins_fields["data"][$i]["value"] = date("U");
		}
	
	} elseif ($fields["data"][$i]["type"] == 'e') {
		include_once('lib/categories/categlib.php');
		$k = $ins_fields["data"][$i]["options"];
		$fields["data"][$i]["$k"] = $categlib->get_child_categories($k);
		$categId = "ins_cat_$k";
		if (isset($_REQUEST[$categId]) and is_array($_REQUEST[$categId])) {
			$ins_categs = array_merge($ins_categs,$_REQUEST[$categId]);
		}
		$ins_fields["data"][$i]["value"] = '';
	
	} elseif ($fields["data"][$i]["type"] == 'c') {
		if (isset($_REQUEST["$ins_id"]) && $_REQUEST["$ins_id"] == 'on') {
			$ins_fields["data"][$i]["value"] = 'y';
		} else {
			$ins_fields["data"][$i]["value"] = 'n';
		}
		if (isset($_REQUEST["$filter_id"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
		} else {
			$fields["data"][$i]["value"] = '';
		}

	} elseif ($fields["data"][$i]["type"] == 'u' and isset($fields["data"][$i]["options"]) and $user)	{
		if (isset($_REQUEST["$ins_id"])) {
			$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
		} else {
			if ($fields["data"][$i]["options"] == 2) {
				$ins_fields["data"][$i]["value"] = $user;
			} elseif ($fields["data"][$i]["options"] == 1) {
				if (isset($tracker_info["writerCanModify"]) and $tracker_info["writerCanModify"] == 'y') {
					$tracker_info["authorfield"] = $fid;
				}
				unset($ins_fields["data"][$i]["fieldId"]);
			} else {
				$ins_fields["data"][$i]["value"] = '';
			}
		}
		if (isset($_REQUEST["$filter_id"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
		} else {
			$fields["data"][$i]["value"] = '';
		}
	
	} elseif ($fields["data"][$i]["type"] == 'g' and isset($fields["data"][$i]["options"]) and $group)	{
		if (isset($_REQUEST["$ins_id"])) {
			$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
		} else {
			if ($fields["data"][$i]["options"] == 2) {
				$ins_fields["data"][$i]["value"] = $group;
			} elseif ($fields["data"][$i]["options"] == 1)  {
				if (isset($tracker_info["writerGroupCanModify"]) and $tracker_info["writerGroupCanModify"] == 'y') {
					$tracker_info["authorgroupfield"] = $fid;
				}
				unset($ins_fields["data"][$i]["fieldId"]);
			} else {
				$ins_fields["data"][$i]["value"] = '';
			}
		}
		if (isset($_REQUEST["$filter_id"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
		} else {
			$fields["data"][$i]["value"] = '';
		}
		
	} else {
		if (isset($_REQUEST["$ins_id"])) {
			$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
		} else {
			$ins_fields["data"][$i]["value"] = '';
		}
		if (isset($_REQUEST["$filter_id"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
		} else {
			$fields["data"][$i]["value"] = '';
		}
		if ($fields["data"][$i]["type"] == 'a' and $fields["data"][$i]["options_array"][0])	{
			$textarea_options = true;
		} elseif ($fields["data"][$i]["type"] == 'i')	{
			if (isset($_FILES["$ins_id"]) && is_uploaded_file($_FILES["$ins_id"]['tmp_name'])) {
				if (!empty($gal_match_regex)) {
					if (!preg_match("/$gal_match_regex/", $_FILES["$ins_id"]['name'], $reqs)) {
						$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
						$smarty->display("error.tpl");
						die;
					}
				}
				if (!empty($gal_nmatch_regex)) {
					if (preg_match("/$gal_nmatch_regex/", $_FILES["$ins_id"]['name'], $reqs)) {
						$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
						$smarty->display("error.tpl");
						die;
					}
				}
				$type = $_FILES["$ins_id"]['type'];
				$size = $_FILES["$ins_id"]['size'];
				$filename = $_FILES["$ins_id"]['name'];
				$ins_fields["data"][$i]["value"] = $_FILES["$ins_id"]['name'];
				$ins_fields["data"][$i]["file_type"] = $_FILES["$ins_id"]['type'];
				$ins_fields["data"][$i]["file_size"] = $_FILES["$ins_id"]['size'];
			}
		}
	}
}

if (isset($tracker_info["authorgroupfield"])) {
	$tracker_info['authorgroup'] = $trklib->get_item_value($_REQUEST["trackerId"],$_REQUEST["itemId"],$tracker_info["authorgroupfield"]);
	if ($tracker_info['authorgroup'] == $group) {
		$tiki_p_modify_tracker_items = 'y';
		$smarty->assign("tiki_p_modify_tracker_items","y");
		$tiki_p_attach_trackers = 'y';
		$smarty->assign("tiki_p_attach_trackers","y");
		$tiki_p_comment_trackers = 'y';
		$smarty->assign("tiki_p_comment_trackers","y");
		$tiki_p_view_trackers = 'y';
		$smarty->assign("tiki_p_view_trackers","y");
	}
} 
if (isset($tracker_info["authorfield"])) {
	$tracker_info['authorindiv'] = $trklib->get_item_value($_REQUEST["trackerId"],$_REQUEST["itemId"],$tracker_info["authorfield"]);
	if ($tracker_info['authorindiv'] == $user) {
		$tiki_p_modify_tracker_items = 'y';
		$smarty->assign("tiki_p_modify_tracker_items","y");
		$tiki_p_attach_trackers = 'y';
		$smarty->assign("tiki_p_attach_trackers","y");
		$tiki_p_comment_trackers = 'y';
		$smarty->assign("tiki_p_comment_trackers","y");
		$tiki_p_view_trackers = 'y';
		$smarty->assign("tiki_p_view_trackers","y");
	}
}
if ($tiki_p_view_trackers != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($mainfield)) {
	$mainfield = $fields["data"][0]["value"];
}
if ($textarea_options) {
	include_once ('lib/quicktags/quicktagslib.php');
	$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','');
	$smarty->assign_by_ref('quicktags', $quicktags["data"]);
}

if ($tiki_p_admin_trackers == 'y') {
	if (isset($_REQUEST["remove"])) {
		check_ticket('view-trackers-items');
		$trklib->remove_tracker_item($_REQUEST["remove"]);
	}
}

if ($tiki_p_modify_tracker_items == 'y') {
	if (isset($_REQUEST["save"])) {
		check_ticket('view-trackers-items');
		if (!isset($_REQUEST["status"])) {
			if (isset($tracker_info["modItemStatus"])) {
				$_REQUEST["status"] = $tracker_info["modItemStatus"];
			} else {
				$_REQUEST["status"] = 'o';
			}
		}
		
		$trklib->replace_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $ins_fields, $_REQUEST["status"]);
		$_REQUEST['show']  = 'view';
		for ($i = 0; $i < count($fields["data"]); $i++) {
			$fid = $fields["data"][$i]["fieldId"];
			$ins_id = 'ins_' . $fid;
			$ins_fields["data"][$i]["value"] = '';
		}
		$item_info = $trklib->get_tracker_item($_REQUEST["itemId"]);
		$smarty->assign('item_info', $item_info);
		
		if (count($ins_categs)) {
			$cat_type = "tracker ".$_REQUEST["trackerId"];
			$cat_objid = $_REQUEST["itemId"];
			$cat_desc = "";
			$cat_name = $mainfield;
			$cat_href = "tiki-view_tracker_item.php?trackerId=".$_REQUEST["trackerId"]."&amp;itemId=".$_REQUEST["itemId"];
			$categlib->uncategorize_object($cat_type, $cat_objid);
			foreach ($ins_categs as $cats) {
				$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
				if (!$catObjectId) {
					$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
				}
				$categlib->categorize($catObjectId, $cats);
			}
		}
	}
}

if ($_REQUEST["itemId"]) {
	$info = $trklib->get_tracker_item($_REQUEST["itemId"]);
	$last = array();
	$lst = '';
	for ($i = 0; $i < count($fields["data"]); $i++) {
		if ($fields["data"][$i]["isPublic"] == 'y' or $tiki_p_admin_trackers) {
			if ($fields["data"][$i]["type"] != 'h') {
				$fid = $fields["data"][$i]["fieldId"];
				$ins_fields["data"][$i]["id"] = $fid;
				if ($fields["data"][$i]["type"] == 'c') {
					if (!isset($info["$fid"])) $info["$fid"] = 'n';
				} else {
					if (!isset($info["$fid"])) $info["$fid"] = '';
				}
				if ($fields["data"][$i]["type"] == 'e') {
					if (!isset($cat)) {
						$cat = $categlib->get_object_categories("tracker ".$_REQUEST["trackerId"],$_REQUEST["itemId"]);
					}
					foreach ($cat as $c) {
						$ins_fields["data"][$i]["cat"]["$c"] = 'y';
					}
				} elseif  ($fields["data"][$i]["type"] == 'l') {
					if (isset($fields["data"][$i]["options_array"][3])) {
						if (isset($last["{$fields["data"][$i]["options_array"][2]}"])) {
							$lst = $last["{$fields["data"][$i]["options_array"][2]}"];
						}
						$ins_fields["data"][$i]['links'] = array();
						if ($lst) {
							$links = $trklib->get_items_list($fields["data"][$i]["options_array"][0],$fields["data"][$i]["options_array"][1],$lst);
							foreach ($links as $link) {
								$ins_fields["data"][$i]['links'][$link] = $trklib->get_item_value($fields["data"][$i]["options_array"][0],$link,$fields["data"][$i]["options_array"][3]);
							}
							$ins_fields["data"][$i]['trackerId'] = $fields["data"][$i]["options_array"][0];
						}
					}
					//ob_start();var_dump($last);$output = ob_get_contents();ob_end_clean();
					//$ins_fields["data"][$i]["links"][] = $output;
				} elseif  ($fields["data"][$i]["type"] == 'r') {
					$ins_fields["data"][$i]["linkId"] = $trklib->get_item_id($ins_fields["data"][$i]["options_array"][0],$ins_fields["data"][$i]["options_array"][1],$info["$fid"]);
					$ins_fields["data"][$i]["value"] = $info["$fid"];
					$ins_fields["data"][$i]["type"] = 't';
				} elseif ($fields["data"][$i]["type"] == 'a') {
					$ins_fields["data"][$i]["value"] = $info["$fid"];
					$ins_fields["data"][$i]["pvalue"] = $tikilib->parse_data($info["$fid"]);
				} else {
					$ins_fields["data"][$i]["value"] = $info["$fid"];
				}
				if (isset($ins_fields["data"][$i]["value"])) {
					$last[$fid] = $ins_fields["data"][$i]["value"];
				}
			}
		}
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

$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);
$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);

$smarty->assign('mail_msg', '');
$smarty->assign('email_mon', '');

if ($user) {
	if (isset($_REQUEST["monitor"])) {
		check_ticket('view-trackers-items');
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
			check_ticket('view-trackers-items');
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
			check_ticket('view-trackers-items');
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
		check_ticket('view-trackers-items');
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
			$trklib->item_attach_file($_REQUEST["itemId"], $name, $type, $size, $data, $_REQUEST["attach_comment"], $user, $fhash,$_REQUEST["attach_version"],$_REQUEST["attach_longdesc"]);
		}
	}
	$attextra = 'n';
	if (strstr($tracker_info["orderAttachments"],'|')) {
		$attextra = 'y';
	}
	$attfields = split(',',strtok($tracker_info["orderAttachments"],'|'));
	$atts = $trklib->list_item_attachments($_REQUEST["itemId"], 0, -1, 'created_desc', '');
	$smarty->assign('atts', $atts["data"]);
	$smarty->assign('attfields', $attfields);
	$smarty->assign('attextra', $attextra);
}

$tabi = 2;
if (isset($_REQUEST['show'])) {
	if ($_REQUEST['show'] == 'view') {
		$tabi = 1;
	} elseif ($tracker_info["useComments"] == 'y' and $_REQUEST['show'] == 'com') {
		if ($tracker_info["useAttachments"] == 'y') $tabi++;
	} elseif ($_REQUEST['show'] == "mod") {
		if ($tracker_info["useAttachments"] == 'y') $tabi++;
		if ($tracker_info["useComments"] == 'y') $tabi++;
	}
	setcookie("activeTabs".urlencode(substr($_SERVER["REQUEST_URI"],1)),"tab$tabi");
} 

$section = 'trackers';
include_once ('tiki-section_options.php');

$smarty->assign('uses_tabs', 'y');

if ($feature_jscalendar) {
	$smarty->assign('uses_jscalendar', 'y');
}
$smarty->assign('daformat', $tikilib->get_long_date_format()." ".tra("at")." %H:%M"); 


ask_ticket('view-trackers-items');

// Display the template
$smarty->assign('mid', 'tiki-view_tracker_item.tpl');
$smarty->display("tiki.tpl");

?>
