<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-newsletters.php,v 1.27 2007-10-12 07:55:29 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/newsletters/nllib.php');

if ($prefs['feature_newsletters'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsletters");

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('confirm', 'n');

//TODO: memorize the charset for each subscription
if (isset($_REQUEST["confirm_subscription"])) {
	$conf = $nllib->confirm_subscription($_REQUEST["confirm_subscription"]);
	if ($conf) {
		$smarty->assign('confirm', 'y');

		$smarty->assign('nl_info', $conf);
	} else {
		$smarty->assign('confirm', 'f');	// Signal failure
	}
}

$smarty->assign('unsub', 'n');

if (isset($_REQUEST["unsubscribe"])) {
	$conf = $nllib->unsubscribe($_REQUEST["unsubscribe"]);
	if ($conf) {
		$smarty->assign('unsub', 'y');

		$smarty->assign('nl_info', $conf);
	} else {
		$smarty->assign('unsub', 'f');	// Signal failure
	}
}

if (!$user && $tiki_p_subscribe_newsletters != 'y' && !isset($_REQUEST["confirm_subscription"])) {
	$smarty->assign('msg', tra("You must be logged in to subscribe to newsletters"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["nlId"])) {
	$_REQUEST["nlId"] = 0;
}

$smarty->assign('nlId', $_REQUEST["nlId"]);

$smarty->assign('subscribe', 'n');
$smarty->assign('subscribed', 'n');

$foo = parse_url($_SERVER["REQUEST_URI"]);
$smarty->assign('url_subscribe', $tikilib->httpPrefix(). $foo["path"]);

if (isset($_REQUEST["nlId"])) {
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
}

if ($user) {
	$user_email = $userlib->get_user_email($user);
} else {
	$user_email = '';
}

$smarty->assign('email', $user_email);

if ($tiki_p_subscribe_newsletters == 'y') {
	if (isset($_REQUEST["subscribe"])) {
		if (empty($user) && $prefs['feature_antibot'] == 'y' && (!isset($_SESSION['random_number']) || $_SESSION['random_number'] != $_REQUEST['antibotcode'])) {
			$smarty->assign('msg', tra("You have mistyped the anti-bot verification code; please try again."));
			$smarty->assign('errortype', 'no_redirect_login');
			$smarty->display("error.tpl");
			die;
		}
	check_ticket('newsletters');

		if ($tiki_p_subscribe_email != 'y') {
			$_REQUEST["email"] = $userlib->get_user_email($user);
		}

		// Now subscribe the email address to the newsletter
		$nl_info = $nllib->get_newsletter($_REQUEST["nlId"]);
		if ($nl_info['allowAnySub'] != 'y' && $user) {
			if ($nllib->newsletter_subscribe($_REQUEST["nlId"], $user, "y"))
				$smarty->assign('subscribed', 'y');
		} elseif ($nllib->newsletter_subscribe($_REQUEST["nlId"], $_REQUEST["email"] ))
			$smarty->assign('subscribed', 'y'); // will receive en email

	}
}

if (isset($_REQUEST["info"])) {
	$nl_info = $nllib->get_newsletter($_REQUEST["nlId"]);

	$smarty->assign('nl_info', $nl_info);
	$smarty->assign('subscribe', 'y');
}
// List newsletters
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

if (isset($_REQUEST["noshowlist"])) {
	$showlist = 'n';
} else {
	// No need to display an empty list to people who can't subscribe
	if ( $tiki_p_subscribe_newsletters != 'y' ) {
		$showlist = 'n';
	} else {
		$showlist = 'y';
	}
}

$smarty->assign('showlist',$showlist);

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $nllib->list_newsletters($offset, $maxRecords, $sort_mode, $find, '', array("tiki_p_subscribe_newsletters", "tiki_p_admin_newsletters", "tiki_p_send_newsletters"));

$smarty->assign_by_ref('cant', $channels['cant']);
$smarty->assign_by_ref('channels', $channels["data"]);

ask_ticket('newsletters');

$section='newsletters';
include_once('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-newsletters.tpl');
$smarty->display("tiki.tpl");

?>
