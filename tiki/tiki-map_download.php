<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-map_download.php,v 1.2 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

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

$smarty->assign('mid', 'map/tiki-map_download.tpl');
$smarty->assign('userwatch', $userwatch);
$map_path = "/var/www/html/map/";

$smarty->display('tiki.tpl');

?>