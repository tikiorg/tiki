<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
		'data' => 'none',
	)),
);

include_once ("tiki-setup.php");
include_once ("lib/csslib.php");

$access->check_feature('feature_editcss');
$access->check_permission('tiki_p_create_css');

if (!isset($_REQUEST['editstyle'])) {
	$editstyle = '';
} else {
	$editstyle = $_REQUEST['editstyle'];
	if (!preg_match('#^[-_a-z\d]+(/[-_a-z\d]+)*$#i', $editstyle)) {
		$smarty->assign('msg', tra('Incorrect name').' '.$editstyle);
		$smarty->display('error.tpl');
		die;
	}
}

$styledir = 'styles';
$style = $csslib->get_nickname_path($editstyle, $styledir);

if (!empty($_REQUEST['edit'])) {
	check_ticket('edit-css');
	if (($data = file_get_contents($style)) === false) {
		$smarty->assign('msg', tra('The specified file does not exist'));
		$smarty->display('error.tpl');
		die;
	}
	$action = 'edit';

} elseif (!empty($_REQUEST['save']) || !empty($_REQUEST['save2'])) {
	check_ticket('edit-css');
	if (file_exists($style)) {
		$stat = stat($style);
		$mod = $stat['mode'] & 0666;
	} else {
		$mod = NULL;
	}
	$style = $csslib->get_nickname_path($editstyle, $styledir, true);
	$fp = fopen($style, "w");
	if (!$fp) {
		$smarty->assign('msg', tra("You do not have permission to write the style sheet")." $style");
		$smarty->display("error.tpl");
		die;
	}

	fwrite($fp, $_REQUEST['data']);
	fclose($fp);
	if ($mod !== NULL) {
		chmod($style, $mod);
	}

	if (!empty($_REQUEST['save2'])) {
		$action = 'display';
		header("location: tiki-edit_css.php?editstyle=$editstyle");
	} else {
		$action = 'edit';
		header("location: tiki-edit_css.php?editstyle=$editstyle&edit=".tra('Edit')."");
	}
	$data = '';
} else {
	$action = 'display';
	$data = '';
}

$smarty->assign('action', $action);
$smarty->assign('data', $data);

if (!empty($editstyle)) {
	$dest = $csslib->get_nickname_path($editstyle, $styledir, true);
	$smarty->assign('writable', file_exists($dest)? is_writable($dest): is_writable(dirname($dest)));
	$cssdata = $csslib->browse_css($style);
	if ((!$cssdata["error"]) and is_array($cssdata["content"])) {
		$parsedcss = $csslib->parse_css($cssdata["content"]);
	} else {
		$parsedcss = $cssdata["error"];
	}
	$smarty->assign('css', $parsedcss);
}

$smarty->assign('editstyle', $editstyle);

if (!empty($_REQUEST['try'])) {
	$style = "$editstyle.css";
	$_SESSION['try_style'] = $style;
	$prefs['style'] = $style;
	header("location: tiki-edit_css.php?editstyle=$editstyle");
}

$list = $csslib->list_css($styledir, true);
if ($tikidomain and is_dir("$styledir/$tikidomain")) {
	$list = array_unique(array_merge($list, $csslib->list_css("$styledir/$tikidomain")));
}
$smarty->assign('list', $list);

ask_ticket('edit-css');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-edit_css.tpl');
$smarty->display("tiki.tpl");
