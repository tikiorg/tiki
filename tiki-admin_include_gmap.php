<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_gmap.php,v 1.2 2006-05-22 17:09:07 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!isset($gmap_defaultx)) {
	$gmap_defaultx = '0';
	$smarty->assign_by_ref('gmap_defaultx',$gmap_defaultx);
}
if (!isset($gmap_defaulty)) {
	$gmap_defaulty = '0';
	$smarty->assign_by_ref('gmap_defaulty',$gmap_defaulty);
}
if (!isset($gmap_defaultz)) {
	$gmap_defaultz = '17';
	$smarty->assign_by_ref('gmap_defaultz',$gmap_defaultz);
}

if (isset($_REQUEST["gmapsetup"])) {
	check_ticket('admin-inc-gmap');

	simple_set_value ("gmap_key");
	simple_set_value ("gmap_defaultx");
	simple_set_value ("gmap_defaulty");
	simple_set_value ("gmap_defaultz");
}
if (strlen($gmap_key) == '86') {
	$smarty->assign('show_map','y');
} else {
	$smarty->assign('show_map','n');
}
ask_ticket('admin-inc-gmap');
?>
