<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-map_edit.php,v 1.3 2003-08-07 10:39:55 franck Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if(@$feature_maps != 'y') {
  $smarty->assign('msg',tra("Feature disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

if ($tiki_p_map_edit != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST["mode"])) {
	$mode = 'listing';
} else {
	$mode = $_REQUEST['mode'];
}

// Validate to prevent editing any file
if (isset($_REQUEST["mapfile"])) {
	if ((substr($_REQUEST["mapfile"], 0, 7) != '../map/') || (strstr(substr($_REQUEST["mapfile"], 3), '..'))) {
		$smarty->assign('msg', tra("You dont have permission to do that"));

		$smarty->display('error.tpl');
		die;
	}
}

$smarty->assign('tiki_p_map_create', $tiki_p_map_create);

if (isset($_REQUEST["create"]) && ($tiki_p_map_create == 'y')) {
	$newmapfile = $_REQUEST["newmapfile"];

	if (!preg_match('/\.map$/i', $newmapfile)) {
		$smarty->assign('msg', tra("mapfile name incorrect"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	$newmapfile = '../map/' . $newmapfile;
	$fp = @fopen($newmapfile, "r");

	if ($fp) {
		$smarty->assign('msg', tra("This mapfile already exists"));

		$smarty->display("styles/$style_base/error.tpl");
		fclose ($fp);
		die;
	}

	$fp = fopen($newmapfile, "w");

	if (!$fp) {
		$smarty->assign('msg', tra("You dont have permission to write the mapfile"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	fclose ($fp);
}

if (isset($_REQUEST["save"])) {
	$fp = fopen($_REQUEST["mapfile"], "w");

	if (!$fp) {
		$smarty->assign('msg', tra("You dont have permission to write the mapfile"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	fwrite($fp, $_REQUEST["data"]);
	fclose ($fp);
}

if (isset($_REQUEST["mapfile"])) {
	$mode = 'editing';

	$fp = fopen($_REQUEST["mapfile"], "r");

	if (!$fp) {
		$smarty->assign('msg', tra("You dont have permission to read the mapfile"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	$data = fread($fp, filesize($_REQUEST["mapfile"]));
	fclose ($fp);
	$smarty->assign('data', $data);
	$smarty->assign('mapfile', $_REQUEST["mapfile"]);
}

$smarty->assign('mode', $mode);

// Get templates from the templates directory
$files = array();
$h = opendir("../map/");

while (($file = readdir($h)) !== false) {
	if (preg_match('/\.map$/i', $file)) {
		$files[] = "../map/" . $file;
	}
}

closedir ($h);

sort ($files);
$smarty->assign('files', $files);

// Get templates from the templates/modules directori
$smarty->assign('mid', 'map/tiki-map_edit.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
