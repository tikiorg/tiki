<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-edit_wiki_section.php,v 1.3.2.3 2008-03-01 17:12:54 leyan Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
global $objectlib; include_once('lib/objectlib.php');
include_once('lib/wiki-plugins/wikiplugin_split.php');

if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['page']))
	$_REQUEST['object'] = $_REQUEST['page'];
if (!isset($_REQUEST['object'])) {
	$smarty->assign('msg', tra('No object indicated'));
	$smarty->display('error.tpl');
	die;
}
if (!isset($_REQUEST['type']) || $_REQUEST['type'] == 'wiki page')
	$_REQUEST['type'] = 'wiki';
$perm = $objectlib->get_needed_perm($_REQUEST['type'], 'edit');
if (!$userlib->user_has_perm_on_object($user, $_REQUEST['object'], $_REQUEST['type'], $perm)) {
	$smarty->assign('msg', tra('Permission denied'));
	$smarty->display('error.tpl');
	die;
}

if (!isset($_REQUEST['referer'])) {
	if (isset($_SERVER['HTTP_REFERER'])) {
		$_REQUEST['referer'] = $_SERVER['HTTP_REFERER'];
	}
}

if (isset($_REQUEST['save'])) {
	check_ticket('edit-wiki-section');
	$info = $objectlib->get_info($_REQUEST['type'], $_REQUEST['object']);
	if (isset($info['error'])) {
		$smarty->assign('msg', tra('Not enable for this type of object'));
		$smarty->display("error.tpl");
		die;
	}
	list($real_start, $real_len) = wikiplugin_split_cell($info['data'], $_REQUEST['pos'], $_REQUEST['cell']); 
	$data = substr($info['data'], 0, $real_start)."\r\n".$_REQUEST['data'].substr($info['data'], $real_start + $real_len);
//echo $_REQUEST['pos']."-".$_REQUEST['cell']."->".$real_start."-".$real_len."<br>".$info['data']."<br>".$data;die;
	$objectlib->set_data($_REQUEST['type'], $_REQUEST['object'], $data);
	header('Location:'.$_REQUEST['referer']);
	die;
} elseif (isset($_REQUEST['cancel_edit'])) {
	header('Location:'.$_REQUEST['referer']);
	die;
} else if (isset($_REQUEST['preview'])) {
	$smarty->assign('preview', 'y');
	$data = $_REQUEST['data'];
	$smarty->assign('parsed', $tikilib->parse_data($data));
	$smarty->assign('title', $_REQUEST['title']);
} else {
	$info = $objectlib->get_info($_REQUEST['type'], $_REQUEST['object']);
	if (isset($info['error'])) {
		$smarty->assign('msg', tra('Not enable for this type of object'));
		$smarty->display("error.tpl");
		die;
	}
	list($real_start, $real_len) = wikiplugin_split_cell($info['data'], $_REQUEST['pos'], $_REQUEST['cell']);
	$data = substr($info['data'], $real_start, $real_len);
	$smarty->assign('title', $info['title']);
}

$smarty->assign_by_ref('data', $data);
$smarty->assign('object', $_REQUEST['object']);
$smarty->assign('type', $_REQUEST['type']);
$smarty->assign('pos', $_REQUEST['pos']);
$smarty->assign('cell', $_REQUEST['cell']);
$smarty->assign('referer', $_REQUEST['referer']);

$section = $_REQUEST['type'];
include_once ('tiki-section_options.php');
include_once("textareasize.php");
include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,100,'taglabel_desc','','wiki');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
ask_ticket('edit-wiki-section');
$smarty->assign('mid', 'tiki-edit_wiki_section.tpl');
$smarty->display("tiki.tpl");
?>
