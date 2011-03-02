<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/map/maplib.php');
if (!isset($prefs['feature_maps']) or $prefs['feature_maps'] != 'y') {
	$smarty->assign('msg', tra("Feature disabled"));
	$smarty->display("error.tpl");
	die;
}
$access->check_permission('tiki_p_map_view');
// Validate to prevent editing any file
if (isset($_REQUEST["mapfile"])) {
	if (strstr($_REQUEST["mapfile"], '..')) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You dont have permission to do that"));
		$smarty->display('error.tpl');
		die;
	}
	$mapfile = $_REQUEST['mapfile'];
} else {
	$mapfile = $prefs['default_map'];
}
if (!isset($prefs['map_path']) or !$prefs['map_path']) {
	$smarty->assign('msg', tra("Maps feature is not correctly setup : Maps path is missing."));
	$smarty->display('error.tpl');
	die;
}
if (!is_dir($prefs['map_path'])) {
	$smarty->assign('msg', tra('Please create a directory named ' . $prefs['map_path'] . ' to hold your map files.'));
	$smarty->display('error.tpl');
	die;
}
if (!preg_match('/\.map$/i', $mapfile)) {
	$smarty->assign('msg', tra("mapfile name incorrect"));
	$smarty->display("error.tpl");
	die;
}
$smarty->assign('mapfile', $mapfile);
//Get the history
// Get mapfiles from the mapfiles directory
$files = $maplib->listMapsWithRev($prefs['map_path']);
$history = array();
$j = 0;
foreach($files as $file) {
	if (substr($file, 0, strlen($mapfile)) == $mapfile) {
		$suffix = substr($file, strlen($mapfile));
		$revision = intval(substr($suffix, 1));
		if ($revision != 0) {
			$history[$j]["version"] = $revision;
			$history[$j]["data"] = nl2br(file_get_contents($prefs['map_path'] . $file));
			$j++;
		}
	}
}
$history[$j]["version"] = $j + 1;
$history[$j]["data"] = nl2br(file_get_contents($prefs['map_path'] . $mapfile));
foreach($history as $index =>$h ) {
	if (strpos($h["data"], "##TIKIMAPS HEADER: END##") != FALSE) {
		$searchdata = substr($h["data"], 0, strpos($h["data"], "##TIKIMAPS HEADER: END##"));
		if (strpos($searchdata, "#IP: ") != FALSE) {
			$IP = substr($searchdata, strpos($searchdata, "#IP: ") + 4);
			$history[$index]["ip"] = substr($IP, 0, strpos($IP, "<br"));
		}
		if (strpos($searchdata, "#Modified by: ") != FALSE) {
			$IP = substr($searchdata, strpos($searchdata, "#Modified by: ") + 13);
			$history[$index]["user"] = substr($IP, 0, strpos($IP, "<br"));
		}
		if (strpos($searchdata, "#GMT Date: ") != FALSE) {
			$IP = substr($searchdata, strpos($searchdata, "#GMT Date: ") + 10);
			$IP = substr($IP, 0, strpos($IP, "<br"));
			$history[$i]["lastModif"] = gmmktime(substr($IP, 10, 2), substr($IP, 12, 2), substr($IP, 14, 2), substr($IP, 5, 2), substr($IP, 7, 2), substr($IP, 1, 4));
		}
	}
}
$smarty->assign_by_ref('history', $history);
$smarty->assign('preview', false);
if (isset($_REQUEST["preview"])) {
	$previewd = $history[$_REQUEST["preview"] - 1]["data"];
	$smarty->assign_by_ref('previewd', $previewd);
	$smarty->assign('preview', $_REQUEST["preview"]);
}
if (isset($_REQUEST["diff2"])) { // previous compatibility
	$_REQUEST["compare"] = "y";
	$_REQUEST["oldver"] = $_REQUEST["diff2"];
}
if (!isset($_REQUEST["newver"])) {
	$_REQUEST["newver"] = count($history);
}
if (isset($_REQUEST["compare"])) {
	foreach($history as $old) {
		if ($old["version"] == $_REQUEST["oldver"]) break;
	}
	$smarty->assign_by_ref('old', $old);
	foreach($history as $new) {
		if ($new["version"] == $_REQUEST["newver"]) break;
	}
	$smarty->assign_by_ref('new', $new);
	if (!isset($_REQUEST["diff_style"]) || $_REQUEST["diff_style"] == "old") $_REQUEST["diff_style"] = 'unidiff';
	$smarty->assign('diff_style', $_REQUEST["diff_style"]);
	if ($_REQUEST["diff_style"] != "sideview") {
		require_once ('lib/diff/difflib.php');
		$html = diff2($old["data"], $new["data"], $_REQUEST["diff_style"]);
		$smarty->assign_by_ref('diffdata', $html);
	}
} else $smarty->assign('diff_style', '');
$section = 'maps';
include_once ('tiki-section_options.php');
// Get templates from the templates/modules directori
$smarty->assign('mid', 'map/tiki-map_history.tpl');
$smarty->display("tiki.tpl");
