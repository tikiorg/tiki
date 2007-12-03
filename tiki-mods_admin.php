<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mods_admin.php,v 1.7.2.1 2007-12-03 22:05:50 marclaporte Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
  
// Initialization
require_once ('tiki-setup.php');
include_once('lib/mods/modslib.php');

if ($tiki_p_admin != 'y') {
  $smarty->assign('msg', tra("You do not have permission to use this feature"));
  $smarty->display("error.tpl");
  die;
}

if (isset($_REQUEST['save'])) {
	if (isset($_REQUEST['feature_mods_provider']) and $_REQUEST['feature_mods_provider'] == 'on') {
		$tikilib->set_preference('feature_mods_provider','y');
	} else {
		$tikilib->set_preference('feature_mods_provider','n');
	}
	if (isset($_REQUEST['mods_dir'])) {
		if (is_dir($_REQUEST['mods_dir'])) {
			$tikilib->set_preference('mods_dir',$_REQUEST['mods_dir']);
		} else {
			$tikifeedback[] = array('num'=>1,'mes'=>"Directory ".$_REQUEST['mods_dir']." not found.");
		}
	} else {
		$tikilib->set_preference('mods_dir','mods');
	}
	if (isset($_REQUEST['mods_server'])) {
		$tikilib->set_preference('mods_server',$_REQUEST['mods_server']);
	} else {
		$tikilib->set_preference('mods_server','http://mods.tikiwiki.org');
	}
}

$smarty->assign('tikifeedback', $tikifeedback);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-mods_admin.tpl');
$smarty->display("tiki.tpl");

?>
