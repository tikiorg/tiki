<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-notepad_write.php,v 1.16 2007-10-12 07:55:29 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$section = 'mytiki';
require_once ('tiki-setup.php');
include_once ('lib/notepad/notepadlib.php');

if ($prefs['feature_notepad'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_notepad");
	$smarty->display("error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("Must be logged to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_notepad != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["noteId"]))
	$_REQUEST["noteId"] = 0;
if (isset($_REQUEST["remove"])) {
	check_ticket('notepad-write');
	$notepadlib->remove_note($user, $_REQUEST['remove']);
}

include 'tiki-parsemode_setup.php';
if ($_REQUEST["noteId"]) {
	$info = $notepadlib->get_note($user, $_REQUEST["noteId"]);
	if ( $info['parse_mode'] == 'raw' ) {
		$info['parsed'] = nl2br(htmlspecialchars($info['data']));
		$smarty->assign('wysiwyg', 'n');
	} else $info['parsed'] = $tikilib->parse_data($info['data'], $is_html);
} else {
	$info = array();
	$info['name'] = '';
	$info['data'] = '';
	$info['parse_mode'] = 'wiki';
}

if (isset($_REQUEST['save'])) {
	check_ticket('notepad-write');
	$_REQUEST["noteId"] = $notepadlib->replace_note($user, $_REQUEST["noteId"], $_REQUEST["name"], $_REQUEST["data"], $_REQUEST["parse_mode"]);
	header ('location: tiki-notepad_read.php?noteId='.$_REQUEST["noteId"]);
	die;
}

$smarty->assign('noteId', $_REQUEST["noteId"]);
$smarty->assign('info', $info);

include_once ('tiki-section_options.php');
include_once ('tiki-mytiki_shared.php');
ask_ticket('notepad-write');

$smarty->assign('mid', 'tiki-notepad_write.tpl');
$smarty->display("tiki.tpl");

?>
