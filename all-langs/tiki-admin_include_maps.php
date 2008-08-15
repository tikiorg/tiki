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

if (isset($_REQUEST["map_path"])){
    if (!is_dir($_REQUEST["map_path"])) {	
			$map_error=tra("Path to mapfiles is invalid");
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

}

$smarty->assign('map_error', $map_error);
?>
