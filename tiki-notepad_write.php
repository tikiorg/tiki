<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'mytiki';
require_once ('tiki-setup.php');
include_once ('lib/notepad/notepadlib.php');
$access->check_feature('feature_notepad');
$access->check_user($user);
$access->check_permission('tiki_p_notepad');
if (isset($_REQUEST["remove"])) {
	check_ticket('notepad-write');
	$notepadlib->remove_note($user, $_REQUEST['remove']);
}
include 'lib/setup/editmode.php';
if (isset($_REQUEST["noteId"])) {
	$info = $notepadlib->get_note($user, $_REQUEST["noteId"]);
	if ($info['parse_mode'] == 'raw') {
		$info['parsed'] = nl2br(htmlspecialchars($info['data']));
		$smarty->assign('wysiwyg', 'n');
	} else $info['parsed'] = $tikilib->parse_data($info['data'], array('is_html' => $is_html));
} else {
	$info = array();
	$info['name'] = '';
	$info['data'] = '';
	$info['parse_mode'] = 'wiki';
}
if (isset($_REQUEST['save'])) {
	check_ticket('notepad-write');
	$noteId = $notepadlib->replace_note($user, isset($_REQUEST["noteId"]) ? $_REQUEST["noteId"] : 0, $_REQUEST["name"], $_REQUEST["data"], $_REQUEST["parse_mode"]);
	header('location: tiki-notepad_read.php?noteId=' . $noteId);
	die;
}
$smarty->assign('noteId', $_REQUEST["noteId"]);
$smarty->assign('info', $info);
include_once ('tiki-section_options.php');
include_once ('tiki-mytiki_shared.php');
ask_ticket('notepad-write');
$smarty->assign('mid', 'tiki-notepad_write.tpl');
$smarty->display("tiki.tpl");
