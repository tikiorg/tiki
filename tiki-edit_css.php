<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_css.php,v 1.15.2.1 2007-11-29 05:19:13 mose Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// $Id: tiki-edit_css.php,v 1.15.2.1 2007-11-29 05:19:13 mose Exp $
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

if ($prefs['feature_editcss'] != 'y') {
	$smarty->assign('msg', tra("Feature disabled"));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_create_css != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["editstyle"]))
	$_REQUEST["editstyle"] = '';

if (!isset($_REQUEST["sub"]))
	$_REQUEST["sub"] = '';

if (!isset($_REQUEST["try"]))
	$_REQUEST["try"] = '';

$editstyle = $_REQUEST["editstyle"];
$styledir = "styles";

if (isset($_REQUEST["edit"])and $_REQUEST["edit"]) {
	$action = 'edit';

	//	$data = implode("",file("$styledir/$editstyle.css"));
	if ($tikidomain and is_file("$styledir/$tikidomain/$editstyle.css")) {
		$data = load_css2_file("$styledir/$tikidomain/$editstyle.css", $styledir);
	} else {
		$data = load_css2_file("$styledir/$editstyle.css", $styledir);
	}
} elseif (isset($_REQUEST["save"])and $_REQUEST["save"]) {
	check_ticket('edit-css');
	$action = 'display';

	$data = '';
	if ($tikidomain and is_dir("$styledir/$tikidomain")) {
		$fp = fopen("$styledir/$tikidomain/$editstyle.css", "w");
	} else {
		$fp = fopen("$styledir/$editstyle.css", "w");
	}

	if (!$fp) {
		$smarty->assign('msg', tra("You do not have permission to write the style sheet"));

		$smarty->display("error.tpl");
		die;
	}

	fwrite($fp, $_REQUEST["data"]);
	fclose ($fp);
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

?>
