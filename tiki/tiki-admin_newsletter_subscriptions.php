<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_newsletter_subscriptions.php,v 1.17 2006-09-19 16:33:13 ohertel Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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
	$smarty->assign('msg', tra("No newsletter indicated"));

	$smarty->display("error.tpl");
	die;
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
	$info["allowAnySub"] = 'n';
	$info["frequency"] = 7 * 24 * 60 * 60;
}

$smarty->assign('nl_info', $info);

if (isset($_REQUEST["remove"])) {
	$area = 'delnlsub';
	if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		if (isset($_REQUEST["email"]))
			$nllib->remove_newsletter_subscription($_REQUEST["remove"], $_REQUEST["email"], "n");
		elseif (isset($_REQUEST["user"]))
			$nllib->remove_newsletter_subscription($_REQUEST["remove"], $_REQUEST["user"], "y");
		elseif (isset($_REQUEST["group"]))
			$nllib->remove_newsletter_group($_REQUEST["remove"], $_REQUEST["group"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["valid"])) {
	check_ticket('admin-nl-subsriptions');
		if (isset($_REQUEST["email"]))
			$nllib->valid_subscription($_REQUEST["valid"], $_REQUEST["email"], "n");
		elseif (isset($_REQUEST["user"]))
			$nllib->valid_subscription($_REQUEST["valid"], $_REQUEST["user"], "y");
}

if (isset($_REQUEST["confirmEmail"]) && $_REQUEST["confirmEmail"] == "on")
	$confirmEmail = "n";
else
	$confirmEmail = $info["validateAddr"];
if (isset($_REQUEST["addemail"]) && $_REQUEST["addemail"] == "y")
	$addEmail = "y";
else
	$addEmail = "n";
if (isset($_REQUEST["add"]) && isset($_REQUEST["email"]) && $_REQUEST["email"] != "") {
	check_ticket('admin-nl-subsriptions');
	$sid = $nllib->newsletter_subscribe($_REQUEST["nlId"], $_REQUEST["email"], "n", $confirmEmail, "");
}
if (isset($_REQUEST["add"]) && isset($_REQUEST['user']) && $_REQUEST['user'] != "") {
	check_ticket('admin-nl-subsriptions');
	$sid = $nllib->newsletter_subscribe($_REQUEST["nlId"], $_REQUEST["user"], "y", $confirmEmail, $addEmail);
}
if (isset($_REQUEST["add"]) && isset($_REQUEST["addall"]) && $_REQUEST["addall"] == "on") {
	check_ticket('admin-nl-subsriptions');
	$nllib->add_all_users($_REQUEST["nlId"], $confirmEmail, $addEmail);
}
if (isset($_REQUEST["add"]) && isset($_REQUEST['group']) && $_REQUEST['group'] != "") {
	check_ticket('admin-nl-subsriptions');
	$nllib->add_group_users($_REQUEST["nlId"], $_REQUEST['group'], $confirmEmail, $addEmail);
}
if (isset($_REQUEST["addbatch"]) && isset($_FILES['batch_subscription']) && $tiki_p_batch_subscribe_email == 'y' && $tiki_p_subscribe_email == 'y') {
    check_ticket('admin-nl-subscription');

    // array with success and errors
    $ok = array();
    $error = array();

    if (!$emails = file($_FILES['batch_subscription']['tmp_name'])) {
	$smarty->assign('msg', tra("Error opening uploaded file"));
	$smarty->display("error.tpl");
	die;
    }

    for ($i = 0; $i<sizeof($emails); $i++) {
	$email = $emails[$i];
	if ($nllib->newsletter_subscribe($_REQUEST["nlId"], $email, 'n', '', 'y')) {
	    $ok[] = $email;
	} else {
	    $error[] = $email;
	}
    }
}
if (isset($_REQUEST["addgroup"]) && isset($_REQUEST['group']) && $_REQUEST['group'] != ""){
	check_ticket('admin-nl-subsriptions');
	$nllib->add_group($_REQUEST["nlId"], $_REQUEST['group']);
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

/* --------------------------------------- */
$sort_mode_g = (isset($_REQUEST["sort_mode_g"]))?$_REQUEST["sort_mode_g"] : 'groupName_asc';
$smarty->assign_by_ref('sort_mode_g', $sort_mode_g);
$offset_g = (isset($_REQUEST["offset_g"]))? $_REQUEST["offset_g"] : 0;
$smarty->assign_by_ref('offset_g', $offset_g);
$find_g = (isset($_REQUEST["find_g"]))? $_REQUEST["find_g"] : '';
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
/* --------------------------------------- */

// Fill array with possible number of questions per page
$freqs = array();

for ($i = 0; $i < 90; $i++) {
	$aux["i"] = $i;

	$aux["t"] = $i * 24 * 60 * 60;
	$freqs[] = $aux;
}

$smarty->assign('freqs', $freqs);

$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);

$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);

/*
$cat_type='newsletter';
$cat_objid = $_REQUEST["nlId"];
include_once("categorize_list.php");
*/
$section = 'newsletters';
include_once ('tiki-section_options.php');

ask_ticket('admin-nl-subsriptions');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_newsletter_subscriptions.tpl');
$smarty->display("tiki.tpl");

?>
