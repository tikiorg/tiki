<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-notepad_write.php,v 1.5 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/notepad/notepadlib.php');

if ($feature_notepad != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("Must be logged to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_notepad != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST["noteId"]))
	$_REQUEST["noteId"] = 0;

if (isset($_REQUEST["remove"])) {
	$notepadlib->remove_note($user, $_REQUEST['remove']);
}

if ($_REQUEST["noteId"]) {
	$info = $notepadlib->get_note($user, $_REQUEST["noteId"]);
} else {
	$info = array();

	$info['name'] = '';
	$info['data'] = '';
}

if (isset($_REQUEST['save'])) {
	$notepadlib->replace_note($user, $_REQUEST["noteId"], $_REQUEST["name"], $_REQUEST["data"]);

	//:TODO: replace this putting the code in notepad-list
	header ('location: tiki-notepad_list.php');
	die;
}

$smarty->assign('noteId', $_REQUEST["noteId"]);
$smarty->assign('info', $info);

include_once ('tiki-mytiki_shared.php');

$smarty->assign('mid', 'tiki-notepad_write.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>