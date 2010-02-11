<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

if (isset($_REQUEST["userfeatures"])) {
	check_ticket('admin-inc-community');
	simple_set_value("user_list_order");
}

// Users Defaults
if (isset($_REQUEST['users_defaults'])) {
	check_ticket('admin-inc-login');
	// numerical and text values
	$_prefs = array(
		'users_prefs_language',
		'users_prefs_mailCharset',
	);

	foreach($_prefs as $pref) {
		simple_set_value($pref);
	}
}

// Users Defaults
$mailCharsets = array(
	'utf-8',
	'iso-8859-1'
);

$smarty->assign_by_ref('mailCharsets', $mailCharsets);
// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages(false, null, true);
$smarty->assign_by_ref('languages', $languages);
ask_ticket('admin-inc-community');
