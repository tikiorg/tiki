<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login_validate.php,v 1.9 2004-04-08 22:55:06 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

$isvalid = false;
if (isset($_REQUEST["user"]) && isset($_REQUEST["pass"])) {
	$isvalid = $userlib->validate_user($_REQUEST["user"], $_REQUEST["pass"], '', '');
}
if ($isvalid) {
	//session_register("user",$_REQUEST["user"]); 
	$_SESSION["$user_cookie_site"] = $_REQUEST["user"];
	$user = $_REQUEST["user"];
	$smarty->assign_by_ref('user', $user);
	//Now since the user is valid we put the user provpassword as the password 
	$userlib->confirm_user($user);
	header ("location: $tikiIndex");
	die;
} else {
	$smarty->assign('msg', tra("Invalid username or password"));

	$smarty->display("error.tpl");
}

?>
