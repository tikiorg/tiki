<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_maps.php,v 1.10 2004-09-28 12:59:13 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
include_once ('lib/map/usermap.php');

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
	      $datastruct="  user Char(20)\n";
  	      $datastruct.="  realName Char(100)\n";
  	      $datastruct.="  avatar Char(250)\n";
  	      // Prepare the data
  	      $query = "select * from `users_users`";
			$result = $tikilib->query($query, array());
			$i=0;
			$data=array();
			while ($res = $result->fetchRow()) {
				$query = "select `value` from `tiki_user_preferences` where (`user` = ?) and (`prefName` = 'lat')";
				$lat = $tikilib->getOne($query,array($res["login"]));
				$query = "select `value` from `tiki_user_preferences` where (`user` = ?) and (`prefName` = 'lon')";
				$lon = $tikilib->getOne($query,array($res["login"]));
				$query = "select `value` from `tiki_user_preferences` where (`user` = ?) and (`prefName` = 'realName')";
				$realName = $tikilib->getOne($query,array($res["login"]));
				if (!isset($realName)) {
					$realName="";
				}
				$login=substr($res["login"],0,20);
				$realName=substr($realName,0,100);
				$image=$tikilib->get_user_avatar($res["login"]);
				if (isset($lat) && isset($lon) && $lat && $lon) {
					$data[$i][0]=$lat;
					$data[$i][1]=$lon;
					$data[$i][2]=$login;
					$data[$i][3]=$realName;
					$data[$i][4]=$image;
					$i++;
				}
			}
			makemap($tdo,$datastruct,$data,3);
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
