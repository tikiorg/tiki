<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_cookies.php,v 1.17 2007-10-12 07:55:23 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/taglines/taglinelib.php');

if ($tiki_p_edit_cookies != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["cookieId"])) {
	$_REQUEST["cookieId"] = 0;
}

$smarty->assign('cookieId', $_REQUEST["cookieId"]);

if ($_REQUEST["cookieId"]) {
	$info = $taglinelib->get_cookie($_REQUEST["cookieId"]);
} else {
	$info = array();

	$info["cookie"] = '';
}

$smarty->assign('cookie', $info["cookie"]);

if (isset($_REQUEST["remove"])) {
	$area = 'delcookie';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$taglinelib->remove_cookie($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["removeall"])) {
	$area = 'delcookieall';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$taglinelib->remove_all_cookies();
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["upload"])) {
	check_ticket('admin-cookies');
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "r");

		while (!feof($fp)) {
			$data = fgets($fp, 65535);

			if (!empty($data)) {
				$data = str_replace("\n", "", $data);

				$taglinelib->replace_cookie(0, $data);
			}
		}

		fclose ($fp);
		$size = $_FILES['userfile1']['size'];
		$name = $_FILES['userfile1']['name'];
		$type = $_FILES['userfile1']['type'];
	} else {
		$smarty->assign('msg', tra("Upload failed"));

		$smarty->display("error.tpl");
		die;
	}
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-cookies');
	$taglinelib->replace_cookie($_REQUEST["cookieId"], $_REQUEST["cookie"]);

	$smarty->assign("cookieId", '0');
	$smarty->assign('cookie', '');
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'cookieId_desc';
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
$channels = $taglinelib->list_cookies($offset, $maxRecords, $sort_mode, $find);

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
ask_ticket('admin-cookies');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_cookies.tpl');
$smarty->display("tiki.tpl");

?>
