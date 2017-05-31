<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
require_once('tiki-setup.php');
$access = TikiLib::lib('access');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));

$modlib = TikiLib::lib('mod');
$usermoduleslib = TikiLib::lib('usermodules');
$userlib = TikiLib::lib('user');
$smarty = TikiLib::lib('smarty');
$tikilib = TikiLib::lib('tiki');

include_once('tiki-module_controls.php');
global $prefs, $user;

clearstatcache();
$modules = $modlib->get_modules_for_user($user);

if (Perms::get()->admin) {
	$smarty->assign('module_pref_errors', $modlib->pref_errors);
}

$show_columns = array_fill_keys(array_keys($modules), 'n');

$modnames = array();
foreach ( $modules as $zone => & $moduleList ) {
	if ($prefs['feature_fullscreen'] != 'y' || empty($_SESSION['fullscreen']) || $_SESSION['fullscreen'] != 'y' ||
			strpos($zone, 'page') === 0) {	// pagetop and pagebottom zones appear in fullscreen

		foreach ( $moduleList as & $mod_reference ) {
			$show_columns[$zone] = 'y';

			$ref = (array) $mod_reference;
			$mod_reference['data'] = new Tiki_Render_Lazy(
				function () use ($ref) {
					$modlib = TikiLib::lib('mod');
					return $modlib->execute_module($ref);
				}
			);
			$modnames[$ref['name']] = '';
		}

		$smarty->assign($zone, $moduleList);
	}
}

//add necessary css files to header as required for specific modules
//TODO only add css when module will actually be showing
$cssadd = array_intersect_key($modlib->cssfiles, $modnames);
if (count($cssadd > 0)) {
	$headerlib = TikiLib::lib('header');
	foreach ($cssadd as $add) {
		$headerlib->add_cssfile($add['csspath'], $add['rank']);
	}
}

$smarty->assign('show_columns', $show_columns);
$smarty->assign('module_zones', $modules);

$module_nodecorations = array('decorations' => 'n');
$module_isflippable = array('flip' => 'y');
$smarty->assign('module_nodecorations', $module_nodecorations);
$smarty->assign('module_isflippable', $module_isflippable);


