<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
include_once ('lib/map/usermap.php');
$map_error = "";
if (isset($_REQUEST["mapuser"])) {
	$map_error = $mapslib->makeusermap();
}
// Setting values if needed
if ((isset($_REQUEST["map_path"])) && (isset($_REQUEST["default_map"])) && (isset($_REQUEST["map_help"])) && (isset($_REQUEST["map_comments"]))) {
	if (($_REQUEST["map_path"] == '') || ($_REQUEST["default_map"] == '') || ($_REQUEST["map_help"] == '') || ($_REQUEST["map_comments"] == '')) {
		$map_error = tra('All Fields except gdaltindex must be filled');
	}
}
if (isset($_REQUEST["map_path"])) {
	if (!is_dir($_REQUEST["map_path"])) {
		$map_error = tra("Path to mapfiles is invalid");
	}
}
if (isset($_REQUEST["gdaltindex"])) {
	if (function_exists("is_executable")) { //linux
		if (! is_executable($_REQUEST["gdaltindex"])) {
			$map_error = tra("No valid gdaltindex executable");
		}
	} else { //windows
		if (! is_file($_REQUEST["gdaltindex"])) {
			$map_error = tra("No valid gdaltindex executable");
		}
	}
}
if (isset($_REQUEST["ogr2ogr"])) {
	if (function_exists("is_executable")) { //linux
		if (! is_executable($_REQUEST["ogr2ogr"])) {
			$map_error = tra("No valid ogr2ogr executable");
		}
	} else { //windows
		if (! is_file($_REQUEST["ogr2ogr"])) {
			$map_error = tra("No valid ogr2ogr executable");
		}
	}
}
$smarty->assign('map_error', $map_error);
