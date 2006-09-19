<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.13 2006-09-19 16:33:17 ohertel Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.13 2006-09-19 16:33:17 ohertel Exp $
include_once ("tiki-setup.php");

if (isset($_REQUEST['user']) && $_REQUEST['user'] == 'admin') {
	$smarty->assign('showloginboxes', 'y');
}

if (isset($_REQUEST['user'])) {
	$smarty->assign('loginuser', $_REQUEST['user']);
}
if (isset($_SERVER['HTTP_REFERER'])) {
	$_url = parse_url($_SERVER['HTTP_REFERER']);
	$_SESSION['loginfrom'] = $_url['path'];
} else {
	$_url = parse_url($tikiIndex);
	$_SESSION['loginfrom'] = $_url['path'];
}
	
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-login.tpl');
$smarty->display("tiki.tpl");

?>