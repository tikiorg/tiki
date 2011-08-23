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

global $userlib, $prefs, $base_url, $headerlib, $smarty;

if (empty($prefs['connect_site_title'])) {
	$defaults = array(
		'connect_site_title' => $prefs['browsertitle'],
		'connect_site_email' => $userlib->get_admin_email(),
		'connect_site_url' => $base_url,
		'connect_site_keywords' => $prefs['metatag_keywords'],
		'connect_site_location' => $prefs['gmap_defaultx'] . ',' . $prefs['gmap_defaulty'] . ',' .$prefs['gmap_defaultz'],
	);
	$smarty->assign('connect_defaults_json', json_encode($defaults));
} else {
	$smarty->assign('connect_defaults_json', '');
}

$headerlib->add_jsfile('lib/jquery_tiki/tiki-connect.js');