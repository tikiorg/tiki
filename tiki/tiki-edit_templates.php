<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_templates.php,v 1.9 2003-12-28 20:12:51 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($feature_edit_templates != 'y') {
	$smarty->assign('msg', tra("Feature disabled"));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_edit_templates != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["mode"])) {
	$mode = 'listing';
} else {
	$mode = $_REQUEST['mode'];
}

// Validate to prevent editing any file
if (isset($_REQUEST["template"])) {
	if ((substr($_REQUEST["template"], 0, 10) != 'templates/') || (strstr($_REQUEST["template"], '..'))) {
		$smarty->assign('msg', tra("You dont have permission to do that"));

		$smarty->display('error.tpl');
		die;
	}
}

if (isset($_REQUEST["save"])) {
	check_ticket('edit-templates');

	$fp = fopen($_REQUEST["template"], "w");

	if (!$fp) {
		$smarty->assign('msg', tra("You dont have permission to write the template"));

		$smarty->display("error.tpl");
		die;
	}
	$_REQUEST["data"] = str_replace("\r\n","\n",$_REQUEST["data"]);
	fwrite($fp, $_REQUEST["data"]);
	fclose ($fp);
}

if (isset($_REQUEST["template"])) {
	$mode = 'editing';

	$fp = fopen($_REQUEST["template"], "r");

	if (!$fp) {
		$smarty->assign('msg', tra("You dont have permission to read the template"));

		$smarty->display("error.tpl");
		die;
	}

	$data = fread($fp, filesize($_REQUEST["template"]));
	fclose ($fp);
	$smarty->assign('data', $data);
	$smarty->assign('template', $_REQUEST["template"]);
}

$smarty->assign('mode', $mode);

// Get templates from the templates directory
$files = array();
$h = opendir("templates");

while (($file = readdir($h)) !== false) {
	if (strstr($file, '.tpl')) {
		$files[] = "templates/" . $file;
	}
}

closedir ($h);
$h = opendir("templates/modules/");

while (($file = readdir($h)) !== false) {
	if (strstr($file, '.tpl')) {
		$files[] = "templates/modules/" . $file;
	}
}

closedir ($h);
$h = opendir("templates/mail/");

while (($file = readdir($h)) !== false) {
	if (strstr($file, '.tpl')) {
		$files[] = "templates/mail/" . $file;
	}
}

closedir ($h);

sort ($files);
$smarty->assign('files', $files);
ask_ticket('edit-templates');

// Get templates from the templates/modules directori
$smarty->assign('mid', 'tiki-edit_templates.tpl');
$smarty->display("tiki.tpl");

?>
