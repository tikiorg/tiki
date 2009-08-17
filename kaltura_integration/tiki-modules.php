<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-modules.php,v 1.69.2.9 2008-03-10 05:43:16 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
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

// additional module zones added to this array will be exposed to tiki.tpl
// TODO change modules user interface to enable additional zones
$module_zones = array(
	'l' => 'left_modules',
	'r' => 'right_modules',
);

$modules = $modlib->get_modules_for_user( $user, $module_zones );
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

