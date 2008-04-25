<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_maps.php,v 1.16 2007-10-12 07:55:24 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
include_once ('lib/map/usermap.php');

$map_error="";

if (isset($_REQUEST["mapzone"]))
{
  $prefs['mapzone']=$_REQUEST["mapzone"];
  $tikilib->set_preference('mapzone',$prefs['mapzone']);
}

if (!isset($prefs['mapzone']))
{
	$prefs['mapzone']=180;
}

$smarty->assign('checkboxes_mapzone', array(
            180 => '[-180 180]',
            360 => '[0 360]'));
$smarty->assign('mapzone_id', $prefs['mapzone']);     

$smarty->assign('map_path', $prefs['map_path']);
$smarty->assign('default_map', $prefs['default_map']);
$smarty->assign('map_help', $prefs['map_help']);
$smarty->assign('map_comments', $prefs['map_comments']);

if (isset($prefs['gdaltindex']))
{
	$smarty->assign('gdaltindex', $prefs['gdaltindex']);
}
if (isset($prefs['ogr2ogr']))
{
	$smarty->assign('ogr2ogr', $prefs['ogr2ogr']);
}

if (isset($_REQUEST["mapuser"])) {
	$map_error=$mapslib->makeusermap();
}

// Setting values if needed
if ((isset($_REQUEST["map_path"])) && (isset($_REQUEST["default_map"]))
     && (isset($_REQUEST["map_help"])) && (isset($_REQUEST["map_comments"]))) {
	$tikilib->set_preference('map_path', $_REQUEST["map_path"]);
	$tikilib->set_preference('default_map', $_REQUEST["default_map"]);
	$tikilib->set_preference('map_help', $_REQUEST["map_help"]);
	$tikilib->set_preference('map_comments', $_REQUEST["map_comments"]);

if (($_REQUEST["map_path"]=='') || ($_REQUEST["default_map"]=='')
     || ($_REQUEST["map_help"]=='') || ($_REQUEST["map_comments"]==''))  {
	$map_error=tra('All Fields except gdaltindex must be filled');  
}
} 

if (isset($_REQUEST["gdaltindex"])) {
	if (function_exists("is_executable")) {  //linux
		if (is_executable($_REQUEST["gdaltindex"])) {
			$tikilib->set_preference('gdaltindex', $_REQUEST["gdaltindex"]);
		} else {
			$map_error=tra("No valid gdaltindex executable");
		}
	} else {  //windows
		if (is_file($_REQUEST["gdaltindex"])) {
			$tikilib->set_preference('gdaltindex', $_REQUEST["gdaltindex"]);
		} else {
			$map_error=tra("No valid gdaltindex executable");
		}	
	}
	$smarty->assign('gdaltindex', $_REQUEST["gdaltindex"]);
}
if (isset($_REQUEST["ogr2ogr"])) {
	if (function_exists("is_executable")) {  //linux
		if (is_executable($_REQUEST["ogr2ogr"])) {
	  		$tikilib->set_preference('ogr2ogr', $_REQUEST["ogr2ogr"]);
		} else {
			$map_error=tra("No valid ogr2ogr executable");
		}

	} else { //windows
			if (is_file($_REQUEST["ogr2ogr"])) {
	  		$tikilib->set_preference('ogr2ogr', $_REQUEST["ogr2ogr"]);
		} else {
			$map_error=tra("No valid ogr2ogr executable");
		}
	}
	$smarty->assign('ogr2ogr', $_REQUEST["ogr2ogr"]);
}

$smarty->assign('map_error', $map_error);

?>
