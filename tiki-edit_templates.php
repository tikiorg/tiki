<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_templates.php,v 1.11 2004-04-26 17:55:12 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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
	if (strstr($_REQUEST["template"], '..')) {
		$smarty->assign('msg', tra("You dont have permission to do that"));
		$smarty->display('error.tpl');
		die;
	}
}

if (isset($_REQUEST["save"])) {
	check_ticket('edit-templates');
  if (isset($tikidomain) and is_file($smarty->template_dir.$tikidomain.'/'.$style_base.'/'.$_REQUEST["template"])) {
    $fp = fopen($smarty->template_dir.$tikidomain.'/'.$style_base.'/'.$_REQUEST["template"], "w");
  } elseif (isset($tikidomain) and is_dir($smarty->template_dir.$tikidomain)) {
    $fp = fopen($smarty->template_dir.$tikidomain.'/'.$_REQUEST["template"], "w");
  } elseif (isset($tikidomain) and is_file($smarty->template_dir.'/'.$style_base.'/'.$_REQUEST["template"])) {
		$fp = fopen($smarty->template_dir.'/'.$style_base.'/'.$_REQUEST["template"], "w");
  } else {
    $fp = fopen($smarty->template_dir.$_REQUEST["template"], "w");
  }
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
	if (isset($tikidomain) and is_file($smarty->template_dir.$tikidomain.'/'.$style_base.'/'.$_REQUEST["template"])) {
		$file = $smarty->template_dir.$tikidomain.'/'.$style_base.'/'.$_REQUEST["template"];
	} elseif (isset($tikidomain) and is_file($smarty->template_dir.$tikidomain.'/'.$_REQUEST["template"])) {
		$file = $smarty->template_dir.$tikidomain.'/'.$_REQUEST["template"];
	} elseif (is_file($smarty->template_dir.'/'.$style_base.'/'.$_REQUEST["template"])) {
		$file = $smarty->template_dir.'/'.$style_base.'/'.$_REQUEST["template"];
	} else {
		$file = $smarty->template_dir.$_REQUEST["template"];
	}
	$fp = fopen($file,'r');
	if (!$fp) {
		$smarty->assign('msg', tra("You dont have permission to read the template"));
		$smarty->display("error.tpl");
		die;
	}
	$data = fread($fp, filesize($file));
	fclose ($fp);
	$smarty->assign('data', $data);
	$smarty->assign('template', $_REQUEST["template"]);
}

$smarty->assign('mode', $mode);

// Get templates from the templates directory
$files = array();
$h = opendir($smarty->template_dir);
while (($file = readdir($h)) !== false) {
	if (substr($file,-4,4) == '.tpl') {
		$files[] = $file;
	}
}
closedir ($h);

$h = opendir($smarty->template_dir."modules/");
while (($file = readdir($h)) !== false) {
	if (substr($file,-4,4) == '.tpl') {
		$files[] = "modules/" . $file;
	}
}
closedir ($h);

$h = opendir($smarty->template_dir."mail/");
while (($file = readdir($h)) !== false) {
	if (substr($file,-4,4) == '.tpl') {
		$files[] = "mail/" . $file;
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
