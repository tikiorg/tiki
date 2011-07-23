<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
if (isset($_REQUEST["connectcomprefs"])) {
	check_ticket('admin-inc-connect');
}
ask_ticket('admin-inc-connect');

global $userlib;
$smarty->assign('def_admin_email', $userlib->get_admin_email());
$smarty->assign('def_loc', $prefs['gmap_defaultx'] . ',' . $prefs['gmap_defaulty'] . ',' .$prefs['gmap_defaultz']);
