<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-map_history.php,v 1.6.2.1 2008-02-27 00:09:14 franck Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once ('lib/map/maplib.php');

if(!isset($prefs['feature_maps']) or $prefs['feature_maps'] != 'y') {
  $smarty->assign('msg',tra("Feature disabled"));
  $smarty->display("error.tpl");
  die;
}

if($tiki_p_map_view != 'y') {
  $smarty->assign('msg',tra("You do not have permissions to view the maps"));
  $smarty->display("error.tpl");
  die;
}

// Validate to prevent editing any file
if (isset($_REQUEST["mapfile"])) {
	if (strstr($_REQUEST["mapfile"], '..')) {
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
	$smarty->assign('msg', tra('Please create a directory named '.$prefs['map_path'].' to hold your map files.'));
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
$files=$maplib->listMapsWithRev($prefs['map_path']);

$history=array();
$j=0;	
for ($i=0;$i<count($files);$i++) {
 	if (substr($files[$i],0,strlen($mapfile))==$mapfile) {
 		$suffix=substr($files[$i],strlen($mapfile));
 		$revision=intval(substr($suffix,1));
 		if ($revision!=0) {
	 		$history[$j]["version"]=$revision;
	 		$history[$j]["data"]=nl2br(file_get_contents($prefs['map_path'].$files[$i]));
 			$j++;
 		}
 	}
}

$history[$j]["version"]=$j+1;
$history[$j]["data"]=nl2br(file_get_contents($prefs['map_path'].$mapfile));

for ($i=0;$i<count($history);$i++) {
	if (strpos($history[$i]["data"],"##TIKIMAPS HEADER: END##")!=FALSE) {
		$searchdata=substr($history[$i]["data"],0,strpos($history[$i]["data"],"##TIKIMAPS HEADER: END##"));
		if (strpos($searchdata,"#IP: ")!=FALSE) {
			$IP=substr($searchdata,strpos($searchdata,"#IP: ")+4);
			$history[$i]["ip"]=substr($IP,0,strpos($IP,"<br"));
		}
		if (strpos($searchdata,"#Modified by: ")!=FALSE) {
			$IP=substr($searchdata,strpos($searchdata,"#Modified by: ")+13);
			$history[$i]["user"]=substr($IP,0,strpos($IP,"<br"));
		}
		if (strpos($searchdata,"#GMT Date: ")!=FALSE) {
			$IP=substr($searchdata,strpos($searchdata,"#GMT Date: ")+10);
			$IP=substr($IP,0,strpos($IP,"<br"));
			$history[$i]["lastModif"]=gmmktime(substr($IP,10,2),substr($IP,12,2),substr($IP,14,2),substr($IP,5,2),substr($IP,7,2),substr($IP,1,4));
		}
	}
}

$smarty->assign_by_ref('history', $history);

$smarty->assign('preview', false);
if (isset($_REQUEST["preview"])) {
	$previewd = $history[$_REQUEST["preview"]-1]["data"];
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
	foreach ($history as $old) {
		if ($old["version"] == $_REQUEST["oldver"])
			break;
	}
	$smarty->assign_by_ref('old', $old);
	foreach ($history as $new) {
		if ($new["version"] == $_REQUEST["newver"])
			break;
	}
	$smarty->assign_by_ref('new', $new);
	if (!isset($_REQUEST["diff_style"]) || $_REQUEST["diff_style"] == "old")
		$_REQUEST["diff_style"] = 'unidiff';
	$smarty->assign('diff_style', $_REQUEST["diff_style"]);
	if ($_REQUEST["diff_style"] != "sideview") {
		require_once('lib/diff/difflib.php');
		$html = diff2($old["data"], $new["data"], $_REQUEST["diff_style"]);
		$smarty->assign_by_ref('diffdata', $html);
	}
}
else
	$smarty->assign('diff_style', '');

$section = 'maps';
include_once ('tiki-section_options.php');

// Get templates from the templates/modules directori
$smarty->assign('mid', 'map/tiki-map_history.tpl');
$smarty->display("tiki.tpl");

?>
