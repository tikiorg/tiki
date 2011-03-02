<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'newsletters';
require_once ('tiki-setup.php');
include_once ('lib/newsletters/nllib.php');
$auto_query_args = array(
	'sort_mode',
	'offset',
	'find',
	'nlId',
	'sort_mode_g',
	'offset_g',
	'find_g'
);

$access->check_feature('feature_newsletters');

if (!isset($_REQUEST["nlId"])) {
	$smarty->assign('msg', tra('No newsletter indicated'));
	$smarty->display('error.tpl');
	die;
}

$info = $nllib->get_newsletter($_REQUEST["nlId"]);

if (empty($info)) {
	$smarty->assign('msg', tra('Newsletter does not exist'));
	$smarty->display('error.tpl');
	die;
}

$smarty->assign('nlId', $_REQUEST["nlId"]);
$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["nlId"], 'newsletter')) {
	$smarty->assign('individual', 'y');
	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'newsletters');
		foreach($perms["data"] as $perm) {
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
$access->check_permission('tiki_p_admin_newsletters');

if (isset($_REQUEST['delsel_x']) && isset($_REQUEST['checked'])) {
	$access->check_authenticity();
	foreach($_REQUEST['checked'] as $check) {
		$nllib->remove_newsletter_subscription_code($check);
	}
}

$smarty->assign('nl_info', $info);
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	if (isset($_REQUEST["email"])) $nllib->remove_newsletter_subscription($_REQUEST["remove"], $_REQUEST["email"], "n");
	elseif (isset($_REQUEST["subuser"])) $nllib->remove_newsletter_subscription($_REQUEST["remove"], $_REQUEST["subuser"], "y");
	elseif (isset($_REQUEST["group"])) $nllib->remove_newsletter_group($_REQUEST["remove"], $_REQUEST["group"]);
	elseif (isset($_REQUEST["included"])) $nllib->remove_newsletter_included($_REQUEST["remove"], $_REQUEST["included"]);
	elseif (isset($_REQUEST['page'])) $nllib->remove_newsletter_page($_REQUEST['remove'], $_REQUEST['page']);
}

if (isset($_REQUEST["valid"])) {
	check_ticket('admin-nl-subsriptions');
	if (isset($_REQUEST["email"])) $nllib->valid_subscription($_REQUEST["valid"], $_REQUEST["email"], "n");
	elseif (isset($_REQUEST["subuser"])) $nllib->valid_subscription($_REQUEST["valid"], $_REQUEST["subuser"], "y");
}

if (isset($_REQUEST["confirmEmail"]) && $_REQUEST["confirmEmail"] == "on") $confirmEmail = "n";
else $confirmEmail = $info["validateAddr"];

if (isset($_REQUEST["addemail"]) && $_REQUEST["addemail"] == "y") $addEmail = "y";
else $addEmail = "n";

if (isset($_REQUEST["add"]) && isset($_REQUEST["email"]) && $_REQUEST["email"] != "") {
	check_ticket('admin-nl-subsriptions');
	if (strpos($_REQUEST["email"], ',')) {
		$emails = explode(',', $_REQUEST["email"]);
		foreach($emails as $e) {
			if ($userlib->user_exists(trim($e))) {
				$nllib->newsletter_subscribe($_REQUEST["nlId"], trim($e) , "y", $confirmEmail, $addEmail);
			} else {
				$nllib->newsletter_subscribe($_REQUEST["nlId"], trim($e) , "n", $confirmEmail, "");
			}
		}
	} else {
		$nllib->newsletter_subscribe($_REQUEST["nlId"], trim($_REQUEST["email"]) , "n", $confirmEmail, "");
	}
}

if (isset($_REQUEST["add"]) && isset($_REQUEST['subuser']) && $_REQUEST['subuser'] != "") {
	check_ticket('admin-nl-subsriptions');
	$sid = $nllib->newsletter_subscribe($_REQUEST["nlId"], $_REQUEST["subuser"], "y", $confirmEmail, $addEmail);
}

if (isset($_REQUEST["add"]) && isset($_REQUEST["addall"]) && $_REQUEST["addall"] == "on") {
	check_ticket('admin-nl-subsriptions');
	$nllib->add_all_users($_REQUEST["nlId"], $confirmEmail, $addEmail);
}

if (isset($_REQUEST["add"]) && isset($_REQUEST['group']) && $_REQUEST['group'] != "") {
	check_ticket('admin-nl-subsriptions');
	$nllib->add_group_users($_REQUEST["nlId"], $_REQUEST['group'], $confirmEmail, $addEmail);
}

if (((isset($_REQUEST["addbatch"]) && isset($_FILES['batch_subscription'])) || (isset($_REQUEST['importPage']) && !empty($_REQUEST['wikiPageName']))) && $tiki_p_batch_subscribe_email == 'y' && $tiki_p_subscribe_email == 'y') {
	check_ticket('admin-nl-subscription');
	// array with success and errors
	$ok = array();
	$error = array();
	if (isset($_REQUEST["addbatch"])) {
		if (!$emails = file($_FILES['batch_subscription']['tmp_name'])) {
			$smarty->assign('msg', tra("Error opening uploaded file"));
			$smarty->display("error.tpl");
			die;
		}
	} else if (isset($_REQUEST["importPage"])) {
		
		$emails = $nllib->get_emails_from_page($_REQUEST['wikiPageName']);
		
		if (!$emails) {
			$smarty->assign('msg', tra('Error importing from wiki page: ') . $_REQUEST['wikiPageName']);
			$smarty->display('error.tpl');
			die;
		}
	}
	
	foreach($emails as $email) {
		$email = trim($email);
		if (empty($email)) continue;
		if ($nllib->newsletter_subscribe($_REQUEST["nlId"], $email, 'n', $confirmEmail, 'y')) {
			$ok[] = $email;
		} else {
			$error[] = $email;
		}
	}
}

if (isset($_REQUEST["addgroup"]) && isset($_REQUEST['group']) && $_REQUEST['group'] != "") {
	check_ticket('admin-nl-subsriptions');
	$nllib->add_group($_REQUEST["nlId"], $_REQUEST['group']);
}

if (isset($_REQUEST["addincluded"]) && isset($_REQUEST['included']) && $_REQUEST['included'] != "") {
	check_ticket('admin-nl-subsriptions');
	$nllib->add_included($_REQUEST["nlId"], $_REQUEST['included']);
}

if (isset($_REQUEST["addPage"]) && !empty($_REQUEST['wikiPageName'])) {
	check_ticket('admin-nl-subsriptions');
	$nllib->add_page($_REQUEST["nlId"], $_REQUEST['wikiPageName'], empty($_REQUEST['noConfirmEmail']) ? 'y' : 'n', empty($_REQUEST['noSubscribeEmail']) ? 'y' : 'n');
}

if (isset($_REQUEST['export'])) {
	check_ticket('admin-nl-subsriptions');
	$users = $nllib->get_all_subscribers($_REQUEST['nlId'], 'y');
	$data = "email\n";
	foreach($users as $u) {
		if (!empty($u['email'])) $data.= $u['email'] . "\n";
	}
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename=' . $info['name'] . '.csv');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0,pre-check=0');
	header('Pragma: public');
	echo $data;
	die;
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'subscribed_desc';
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
$channels = $nllib->list_newsletter_subscriptions($_REQUEST["nlId"], $offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
$sort_mode_g = (isset($_REQUEST["sort_mode_g"])) ? $_REQUEST["sort_mode_g"] : 'groupName_asc';
$smarty->assign_by_ref('sort_mode_g', $sort_mode_g);
$offset_g = (isset($_REQUEST["offset_g"])) ? $_REQUEST["offset_g"] : 0;
$smarty->assign_by_ref('offset_g', $offset_g);
$find_g = (isset($_REQUEST["find_g"])) ? $_REQUEST["find_g"] : '';
$smarty->assign('find_g', $find_g);
$groups_g = $nllib->list_newsletter_groups($_REQUEST["nlId"], $offset_g, $maxRecords, $sort_mode_g, $find_g);
$cant_pages_g = ceil($groups_g["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages_g', $cant_pages_g);
$smarty->assign('actual_page_g', 1 + ($offset_g / $maxRecords));

if ($groups_g["cant"] > ($offset_g + $maxRecords)) {
	$smarty->assign('next_offset_g', $offset_g + $maxRecords);
} else {
	$smarty->assign('next_offset_g', -1);
}

if ($offset_g > 0) {
	$smarty->assign('prev_offset_g', $offset_g - $maxRecords);
} else {
	$smarty->assign('prev_offset_g', -1);
}

$smarty->assign_by_ref('groups_g', $groups_g["data"]);
$smarty->assign("nb_groups", $groups_g["cant"]);
$included_n = $nllib->list_newsletter_included($_REQUEST["nlId"], 0, -1);
$smarty->assign('included_n', $included_n);
$smarty->assign('nb_included', count($included_n));
$pages = $nllib->list_newsletter_pages($_REQUEST["nlId"], 0, -1);
$smarty->assign('pages', $pages['data']);
$smarty->assign('nb_pages', $pages['cant']);

$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);
$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);
$newsletters = $nllib->list_newsletters(0, -1, "created_desc", false, '', '', 'n');
$smarty->assign_by_ref('newsletters', $newsletters['data']);
include_once ('tiki-section_options.php');
ask_ticket('admin-nl-subsriptions');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_newsletter_subscriptions.tpl');
$smarty->display("tiki.tpl");
