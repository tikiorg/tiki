<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_gmap.php,v 1.4 2006-12-11 22:36:15 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["gmapsetup"])) {
	check_ticket('admin-inc-gmap');
	simple_set_value ("gmap_key");
	simple_set_value ("gmap_defaultx");
	simple_set_value ("gmap_defaulty");
	simple_set_value ("gmap_defaultz");
}
if (isset($gmap_key) and strlen($gmap_key) == '86') {
	$smarty->assign('show_map','y');
} else {
	$smarty->assign('show_map','n');
}
ask_ticket('admin-inc-gmap');
?>
