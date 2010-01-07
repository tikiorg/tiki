<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_gmap.php,v 1.6 2007-10-12 07:55:24 nyloth Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
if (isset($_REQUEST["gmapsetup"])) {
	check_ticket('admin-inc-gmap');
	simple_set_value("gmap_key");
	simple_set_value("gmap_defaultx");
	simple_set_value("gmap_defaulty");
	simple_set_value("gmap_defaultz");
}
if (isset($prefs['gmap_key']) and strlen($prefs['gmap_key']) == '86') {
	$smarty->assign('show_map', 'y');
} else {
	$smarty->assign('show_map', 'n');
}
ask_ticket('admin-inc-gmap');
