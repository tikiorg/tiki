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

if (isset($prefs['gmap_key']) and strlen($prefs['gmap_key']) == '86') {
	$smarty->assign('show_map', 'y');
	$headerlib->add_jsfile('http://maps.google.com/maps?file=api&amp;v=2&key=' . $prefs['gmap_key'], 'external');
} else {
	$smarty->assign('show_map', 'n');
}
ask_ticket('admin-inc-gmap');
