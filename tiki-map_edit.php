<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-map_edit.php,v 1.5 2003-08-26 05:51:32 franck Exp $

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



if (!isset($_REQUEST["mode"])) {
	$mode = 'listing';
} else {
	$mode = $_REQUEST['mode'];
}

// Validate to prevent editing any file
if (isset($_REQUEST["mapfile"])) {
	if (strstr($_REQUEST["mapfile"], '..')) {
		$smarty->assign('msg', tra("You dont have permission to do that"));
		$smarty->display('error.tpl');
		die;
	}
}

$smarty->assign('tiki_p_map_create', $tiki_p_map_create);

if (isset($_REQUEST["create"]) && ($tiki_p_map_create == 'y')) {
	$newmapfile = $map_path.$_REQUEST["newmapfile"];

	if (!preg_match('/\.map$/i', $newmapfile)) {
		$smarty->assign('msg', tra("mapfile name incorrect"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

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
$smarty->assign('tiki_p_map_delete', $tiki_p_map_delete);
if ((isset($_REQUEST["delete"])) && ($tiki_p_map_delete == 'y')) {
  if(! unlink($map_path.$_REQUEST["mapfile"]))
  {
		$smarty->assign('msg', tra("You dont have permission to delete the mapfile"));
		$smarty->display("styles/$style_base/error.tpl");  
		die;
  }
  $mode='listing';
}

if (isset($_REQUEST["save"])) {
if ($tiki_p_map_edit != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("styles/$style_base/error.tpl");
	die;
}
	$fp = fopen($map_path.$_REQUEST["mapfile"], "w");

	if (!$fp) {
		$smarty->assign('msg', tra("You dont have permission to write the mapfile"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	fwrite($fp, $_REQUEST["data"]);
	fclose ($fp);
}

if ((isset($_REQUEST["mapfile"])) && ($mode=='editing')) {
if ($tiki_p_map_edit != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("styles/$style_base/error.tpl");
	die;
}
 $mapfile = $map_path .$_REQUEST["mapfile"];
 
	$fp = fopen($mapfile, "r");

	if (!$fp) {
		$smarty->assign('msg', tra("You dont have permission to read the mapfile"));
		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	$data = fread($fp, filesize($mapfile));
	fclose ($fp);
	$smarty->assign('data', $data);
	$smarty->assign('mapfile', $_REQUEST["mapfile"]);
}

$smarty->assign('mode', $mode);

// Get mapfiles from the mapfiles directory
$files = array();
$h = opendir($map_path);

while (($file = readdir($h)) !== false) {
	if (preg_match('/\.map$/i', $file)) {
		$files[] = $file;
	}
}

closedir ($h);

sort ($files);
$smarty->assign('files', $files);
$smarty->assign('tiki_p_map_edit', $tiki_p_map_edit);

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-map_edit.php", "tiki-map.phtml", $foo["path"]);
$smarty->assign('url_browse', httpPrefix(). $foo1);

// Get templates from the templates/modules directori
$smarty->assign('mid', 'map/tiki-map_edit.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
