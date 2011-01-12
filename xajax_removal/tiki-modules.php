<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
require_once('tiki-setup.php');
global $modlib; include_once('lib/modules/modlib.php');
global $access;
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $usermoduleslib; include_once ('lib/usermodules/usermoduleslib.php');
include_once('tiki-module_controls.php');
global $prefs, $user, $userlib, $tiki_p_admin, $tiki_p_configure_modules, $smarty, $tikidomain, $tikilib, $section, $page, $user_groups;

clearstatcache();

if ($tiki_p_admin != 'y') {
	$user_groups = $tikilib->get_user_groups($user);
} else {
	$user_groups = array();
}

$modules = $modlib->get_modules_for_user( $user );
record_module_loading_errors();

$show_columns = array_fill_keys( array_keys( $modules ), 'n' );

foreach( $modules as $zone => & $moduleList ) {
	foreach( $moduleList as & $mod_reference ) {
		$show_columns[$zone] = 'y';

		$mod_reference['data'] = $modlib->execute_module( $mod_reference );
	}

	$smarty->assign_by_ref( $zone, $moduleList );
}

$smarty->assign_by_ref( 'show_columns', $show_columns );

$module_nodecorations = array('decorations' => 'n');
$module_isflippable = array('flip' => 'y');
$smarty->assign('module_nodecorations', $module_nodecorations);
$smarty->assign('module_isflippable', $module_isflippable);


function record_module_loading_errors() {
	global $user, $modlib, $tikilib, $smarty;
	$user_groups = $tikilib->get_user_groups($user);
	if (in_array('Admins', $user_groups)) {
		$smarty->assign('module_pref_errors', $modlib->pref_errors);
	}
}
