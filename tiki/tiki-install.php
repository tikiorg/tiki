<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-install.php,v 1.80 2006-09-19 16:33:16 ohertel Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if (!file_exists("installer/tiki-installer.php")) {
	header ("Status: 410 Gone");
	header ("HTTP/1.0 410 Gone"); 
	header ('location: index.php');
	die('TikiWiki installer has been disabled.');
} else {
	include_once("installer/tiki-installer.php");
}

//Load Profiles
load_profiles();

//Load SQL scripts
load_sql_scripts();

// If no admin account then allow the creation of an admin account
if ($admin_acc == 'n' && isset($_REQUEST['createadmin'])) {
	if ($_REQUEST['pass1'] == $_REQUEST['pass2']) {
		$hash = md5($_REQUEST['pass1']);
		//$query = "delete from users_users where login='admin'";
		//$dbTiki->Execute($query);
		$pass1 = addslashes($_REQUEST['pass1']);
		$query = "insert into users_users(login,password,hash) values('admin','$pass1','$hash')";
		$dbTiki->Execute($query);
		$admin_acc = 'y';
	}
}

$smarty->assign('admin_acc', $admin_acc);

// Since we do have an admin account the user must login to
// use the install script
if (isset($_REQUEST['login'])) {
	$tikifeedback[] = check_password();
}

// If no admin account then we are logged
if ($admin_acc=='n') {
	$_SESSION["install-logged-$multi"] = 'y';
}

$smarty->assign('dbdone', 'n');
$smarty->assign('logged', $logged);

if (isset($_SESSION["install-logged-$multi"]) && $_SESSION["install-logged-$multi"] == 'y') {
	$smarty->assign('logged', 'y');

	if (isset($_REQUEST['scratch'])) {
		process_sql_file ('tiki-' . $dbversion_tiki . "-" . $db_tiki . '.sql',$db_tiki);

		$smarty->assign('dbdone', 'y'); if (isset($_REQUEST['profile'])) {
			process_sql_file ('profiles/' . $_REQUEST['profile'],$db_tiki);
			//$profile = $_REQUEST['profile'];
			//print "Profile: $profile";
		}
	}

	if (isset($_REQUEST['update'])) {
		process_sql_file ($_REQUEST['file'],$db_tiki);

		$smarty->assign('dbdone', 'y');
	}
}
$smarty->assign_by_ref('tikifeedback', $tikifeedback);

// getting memory_limit from PHP
$php_memory_limit = ini_get('memory_limit');
$smarty->assign('php_memory_limit', $php_memory_limit);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->display("tiki.tpl");

//print "<hr>";
//setup_help();
?>
