<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_maps.php,v 1.1 2003-08-08 04:48:15 franck Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$smarty->assign('map_path', $map_path);
$smarty->assign('default_map', $default_map);
if ((isset($_REQUEST["map_path"])) && (isset($_REQUEST["default_map"]))) {
	$tikilib->set_preference('map_path', $_REQUEST["map_path"]);
	$tikilib->set_preference('default_map', $_REQUEST["default_map"]);
	$smarty->assign('map_path', $_REQUEST["map_path"]);
	$smarty->assign('default_map', $_REQUEST["default_map"]);
}

?>