<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
if (!isset($_REQUEST["noteId"])) {
	$smarty->assign('msg', tra("No note indicated"));
	$smarty->display("error.tpl");
	die;
}
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity(tra('Are you sure you want to delete this note?'));
	$notepadlib->remove_note($user, $_REQUEST['noteId']);
	header('location: tiki-notepad_list.php');
	die;
}
$info = $notepadlib->get_note($user, $_REQUEST["noteId"]);
if (!$info) {
	$smarty->assign('msg', tra("Note not found"));
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['wikify']) || isset($_REQUEST['over'])) {
	check_ticket('notepad-read');
	if (empty($_REQUEST['wiki_name'])) {
		$smarty->assign('msg', tra("No name indicated for wiki page"));
		$smarty->display("error.tpl");
		die;
	}
	if ($tikilib->page_exists($_REQUEST['wiki_name'])) {
		if (isset($_REQUEST['over'])) {
			$pageperms = $tikilib->get_perm_object($_REQUEST['wiki_name'], 'wiki page', '', false);
			if ($pageperms["tiki_p_edit"] == 'y') {
				$tikilib->update_page($_REQUEST['wiki_name'], $info['data'], tra('created from notepad'), $user, '127.0.1.1', $info['name']);
			} else {
				$smarty->assign('errortype', 401);
				$smarty->assign('msg', tra("You do not have permission to edit this page."));
				$smarty->display("error.tpl");
				die;
			}
		} else {
			$smarty->assign('msg', tra("Page already exists"));
			$smarty->display("error.tpl");
			die;
		}
	} else {
		if ($tiki_p_edit == 'y') {
			$tikilib->create_page($_REQUEST['wiki_name'], 0, $info['data'], $tikilib->now, tra('created from notepad'), $user, $ip = '0.0.0.0', $info['name']);
		} else {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to edit this page."));
			$smarty->display("error.tpl");
			die;
		}
	}
}

if ($tikilib->page_exists($info['name'])) {
	$smarty->assign("wiki_exists", "y");
} else {
	$smarty->assign("wiki_exists", "n");
}
if (isset($_REQUEST['parse_mode']) and $_REQUEST['parse_mode'] != $info['parse_mode']) {
	$notepadlib->set_note_parsing($user, $_REQUEST['noteId'], $_REQUEST['parse_mode']);
	$info['parse_mode'] = $_REQUEST['parse_mode'];
}
if ($info['parse_mode'] == 'raw') {
	$info['parsed'] = nl2br(htmlspecialchars($info['data']));
	$smarty->assign('wysiwyg', 'n');
} else {
	include 'lib/setup/editmode.php';
	$info['parsed'] = $tikilib->parse_data($info['data'], array('is_html' => $is_html));
}
$smarty->assign('noteId', $_REQUEST["noteId"]);
$smarty->assign('info', $info);
include_once ('tiki-section_options.php');
include_once ('tiki-mytiki_shared.php');
ask_ticket('notepad-read');
$smarty->assign('mid', 'tiki-notepad_read.tpl');
$smarty->display("tiki.tpl");
