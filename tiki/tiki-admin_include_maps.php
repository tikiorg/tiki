<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_maps.php,v 1.9 2004-09-08 19:51:49 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
include('lib/map/usermap.php');

$map_error="";

$smarty->assign('map_path', $map_path);
$smarty->assign('default_map', $default_map);
$smarty->assign('map_help', $map_help);
$smarty->assign('map_comments', $map_comments);

if (isset($gdaltindex))
{
	$smarty->assign('gdaltindex', $gdaltindex);
}
if (isset($ogr2ogr))
{
	$smarty->assign('ogr2ogr', $ogr2ogr);
}

if (isset($_REQUEST["mapuser"])) {
	if (isset($ogr2ogr) && is_executable($ogr2ogr)) {
		// User preferences screen
		if ($feature_userPreferences != 'y') {
			$map_error=tra("This feature is disabled").": feature_userPreferences";
		} else {
			$tdo = "user";
			if ($tikidomain) $tdo = "$tikidomain.user";
		   $datastruct="Columns 2\n";
	      $datastruct.="  user Char(20)\n";
  	      $datastruct.="  realName Char(100)\n";
			makemap($tdo,$datastruct);
		}
	} else {
		$map_error=tra("No valid ogr2ogr executable");
	}
}

// Setting values if needed
if ((isset($_REQUEST["map_path"])) && (isset($_REQUEST["default_map"]))
     && (isset($_REQUEST["map_help"])) && (isset($_REQUEST["map_comments"]))) {
	$tikilib->set_preference('map_path', $_REQUEST["map_path"]);
	$tikilib->set_preference('default_map', $_REQUEST["default_map"]);
	$tikilib->set_preference('map_help', $_REQUEST["map_help"]);
	$tikilib->set_preference('map_comments', $_REQUEST["map_comments"]);
	$smarty->assign('map_path', $_REQUEST["map_path"]);
	$smarty->assign('default_map', $_REQUEST["default_map"]);
	$smarty->assign('map_help', $_REQUEST["map_help"]);	
	$smarty->assign('map_comments', $_REQUEST["map_comments"]);

if (($_REQUEST["map_path"]=='') || ($_REQUEST["default_map"]=='')
     || ($_REQUEST["map_help"]=='') || ($_REQUEST["map_comments"]==''))  {
	$smarty->assign('map_error', tra('All Fields except gdaltindex must be filled'));  
}
} 

if (isset($_REQUEST["gdaltindex"])) {
	if (is_executable($_REQUEST["gdaltindex"])) {
		$tikilib->set_preference('gdaltindex', $_REQUEST["gdaltindex"]);
	} else {
		$map_error=tra("No valid gdaltindex executable");
	}
	$smarty->assign('gdaltindex', $_REQUEST["gdaltindex"]);
}
if (isset($_REQUEST["ogr2ogr"])) {
	if (is_executable($_REQUEST["ogr2ogr"])) {
  		$tikilib->set_preference('ogr2ogr', $_REQUEST["ogr2ogr"]);
	} else {
		$map_error=tra("No valid ogr2ogr executable");
	}
  	$smarty->assign('ogr2ogr', $_REQUEST["ogr2ogr"]);
}

$smarty->assign('map_error', $map_error);

?>
