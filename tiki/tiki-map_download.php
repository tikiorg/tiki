<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-map_download.php,v 1.4 2003-08-19 00:15:26 franck Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

if(@$feature_maps != 'y') {
  $smarty->assign('msg',tra("Feature disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}
      
if (isset($_REQUEST['view_user'])) {
	$userwatch = $_REQUEST['view_user'];
} else {
	if ($user) {
		$userwatch = $user;
	} else {
		$smarty->assign('msg', tra("You are not logged in and no user indicated"));
		$smarty->display("styles/$style_base/error.tpl");
		die;
	}
}

if (!isset($_REQUEST['mapfile']) || !isset($_REQUEST['layer']))
{
		$smarty->assign('msg', tra("You have not specified a mapfile and a layer"));
		$smarty->display("styles/$style_base/error.tpl");
		die;
} else
{
  $smarty->assign('mapfile', $mapfile);
  $smarty->assign('layer', $layer);
}




$smarty->assign('mid', 'map/tiki-map_download.tpl');
$smarty->assign('userwatch', $userwatch);
$map_path = "/var/www/html/map/";

$smarty->display('tiki.tpl');

?>
