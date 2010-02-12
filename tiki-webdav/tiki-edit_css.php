<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once ("tiki-setup.php");
include_once ("lib/csslib.php");

//
// Load CSS2 styled file (@import aware)
//
// TODO: Will M$ windowz eat '/' as path delimiter?
//
function load_css2_file($filename, $styledir) {
	$data = '';

	$lines = file($filename);

	//
	foreach ($lines as $line) {
	/*
		if (preg_match_all("/@import( |\t)+('|\")(.*)(|\")( |\t)*;/U", $line, $importfiles, PREG_SET_ORDER)) {
			foreach ($importfiles as $file) {
				$import = $styledir . '/' . $file[3];

				$data .= load_css2_file($import, substr($import, 0, strrpos($import, "/")));
				$line = str_replace($file[0], "", $line);
			}
		}
	*/
		// TODO: Does it matter what $line may contain smth before '@import'? :)
		$data .= $line;
	}

	return $data;
}

if (!isset($prefs['feature_editcss']))
	$prefs['feature_editcss'] = 'n';

if (!isset($tiki_p_create_css))
	$tiki_p_create_css = 'n';
$access->check_feature('feature_editcss');
$access->check_permission('tiki_p_create_css');

if (!isset($_REQUEST["editstyle"]))
	$_REQUEST["editstyle"] = '';

if (!isset($_REQUEST["sub"]))
	$_REQUEST["sub"] = '';

if (!isset($_REQUEST["try"]))
	$_REQUEST["try"] = '';

$editstyle = preg_replace("/[^-_a-z\d]/i","",$_REQUEST["editstyle"]);
$styledir = "styles";

function get_style_path($editstyle, $styledir) {
    global $tikidomain;
	if ($tikidomain and is_file("$styledir/$tikidomain/$editstyle.css")) {
		return "$styledir/$tikidomain/$editstyle.css";
	} else {
		return "$styledir/$editstyle.css";
	}    
}

function get_style_mod($editstyle, $styledir) {
	$style=get_style_path($editstyle, $styledir);
	$stat=stat($style);
	return $stat['mode'] & 0666;
}

if (isset($_REQUEST["edit"])and $_REQUEST["edit"]) {

	$action = 'edit';
	$data = load_css2_file(get_style_path($editstyle, $styledir), $styledir);

} elseif ((isset($_REQUEST["save"]) and $_REQUEST["save"]) or (isset($_REQUEST["save2"]) and $_REQUEST["save2"])) {
	check_ticket('edit-css');
	$action = 'edit';

	$data = '';
	if ($tikidomain and is_dir("$styledir/$tikidomain")) {
		$style = "$styledir/$tikidomain/$editstyle.css";
	} else {
		$style = "$styledir/$editstyle.css";
	}

	$mod=NULL;
	$mod = get_style_mod($editstyle, $styledir);
	$fp = fopen($style, "w");
	if (!$fp) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to write the style sheet")." $style");

		$smarty->display("error.tpl");
		die;
	}

	fwrite($fp, $_REQUEST["data"]);
	fclose ($fp);
	if ($mod !== NULL) {
		chmod($style, $mod);
	}

	if ($_REQUEST["save2"]) {
		$action = 'display';
		header("location: tiki-edit_css.php?editstyle=$editstyle");
	} else {
		header("location: tiki-edit_css.php?editstyle=$editstyle&edit=".tra('Edit')."");
	}
} else {
	$action = 'display';

	$data = '';
}

$smarty->assign('action', $action);
$smarty->assign('data', $data);

if ($tikidomain and is_file("$styledir/$tikidomain/$editstyle.css")) {
	$cssdata = $csslib->browse_css("$styledir/$tikidomain/$editstyle.css");
} else {
	$cssdata = $csslib->browse_css("$styledir/$editstyle.css");
}
if ((!$cssdata["error"]) and is_array($cssdata["content"])) {
	$parsedcss = $csslib->parse_css($cssdata["content"]);
} else {
	$parsedcss = $cssdata["error"];
}

$smarty->assign('css', $parsedcss);
$smarty->assign('editstyle', $editstyle);

if ($_REQUEST["try"]) {
	$style = "$editstyle.css";
	$_SESSION['try_style'] = $style;
	$prefs['style'] = $style;
}

$list = $csslib->list_css($styledir);
if ($tikidomain and is_dir("$styledir/$tikidomain")) {
	$list = array_unique(array_merge($list,$csslib->list_css("$styledir/$tikidomain")));
}
$smarty->assign('list', $list);

ask_ticket('edit-css');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-edit_css.tpl');
$smarty->display("tiki.tpl");
