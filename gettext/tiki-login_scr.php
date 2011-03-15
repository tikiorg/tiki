<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section_class = 'tiki_login';	// This will be body class instead of $section
include_once ("tiki-setup.php");
if (isset($_REQUEST['user'])) {
	if ($_REQUEST['user'] == 'admin' && $_SESSION["groups_are_emulated"] != "y") $smarty->assign('showloginboxes', 'y');
	else $smarty->assign('loginuser', $_REQUEST['user']);
}
if ($prefs['useGroupHome'] != 'y' && !isset($_SESSION['loginfrom'])) {
	$_SESSION['loginfrom'] = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $prefs['tikiIndex']);
}

$headerlib->add_js( '$(document).ready( function() {$("#login-user").focus().select();} );' );

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('headtitle', tra('Log In'));
$smarty->assign('mid', 'tiki-login.tpl');
$smarty->display("tiki.tpl");
