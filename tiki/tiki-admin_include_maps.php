<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_maps.php,v 1.3 2003-11-13 05:55:46 franck Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$smarty->assign('map_path', $map_path);
$smarty->assign('default_map', $default_map);
$smarty->assign('map_help', $map_help);
$smarty->assign('map_comments', $map_comments);
if (isset($gdaltindex))
{
  $smarty->assign('gdaltindex', $gdaltindex);
}
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
  $tikilib->set_preference('gdaltindex', $_REQUEST["gdaltindex"]);
  $smarty->assign('gdaltindex', $_REQUEST["gdaltindex"]);
}



?>