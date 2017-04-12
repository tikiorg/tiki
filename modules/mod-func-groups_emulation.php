<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @return array
 */
function module_groups_emulation_info()
{
	return array(
		'name' => tra('Groups Emulation'),
		'description' => tra('Enables temporarily changing one\'s group memberships to see how users in fewer groups experience the site.'),
		'prefs' => array(),
		'params' => array(
			'showallgroups' => array(
				'name' => tra('Show All Groups'),
				'description' => tra('Show All Groups') . '. ' . tra('If set to "n", the list is not shown.'),
				'filter' => 'alpha',
				'default' => 'y',
				'since' => '13.1',
				'options' => array(
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'showyourgroups' => array(
				'name' => tra('Show Your Groups'),
				'description' => tra('Show Your Groups') . '. ' . tra('If set to "n", the list is not shown.'),
				'filter' => 'alpha',
				'default' => 'y',
				'since' => '13.1',
				'options' => array(
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
		),
		'common_params' => array('rows')
		
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_groups_emulation($mod_reference, $module_params)
{
	global $user, $tiki_p_admin;
	$userlib = TikiLib::lib('user');
	$smarty = TikiLib::lib('smarty');
	
	$smarty->assign('groups_are_emulated', isset($_SESSION['groups_are_emulated']) ? $_SESSION['groups_are_emulated'] : 'n');
	if (isset($_SESSION['groups_emulated']))
		$smarty->assign('groups_emulated', unserialize($_SESSION['groups_emulated']));
	
	// Admins can see all existing groups
	$allGroups = array();
	if ($tiki_p_admin == 'y') {
		$alls = $userlib->get_groups();
		foreach ($alls['data'] as $g) {
			$allGroups[$g['groupName']] = "real";
		}
		$smarty->assign_by_ref('allGroups', $allGroups);
	}
	
	// Extract list of groups of user, including included groups
	$userGroups = $userlib->get_user_groups_inclusion($user);
	if ($tiki_p_admin == 'y') {
		$chooseGroups = $allGroups;
	} else {
		$chooseGroups = $userGroups;
	}
	$chooseGroups["Anonymous"] = "included";
	if (isset($user)) {
		$chooseGroups["Registered"] = "included";
	}
	$smarty->assign_by_ref('userGroups', $userGroups);
	$smarty->assign_by_ref('chooseGroups', $chooseGroups);
	$smarty->assign('showallgroups', isset($module_params['showallgroups']) ? $module_params['showallgroups'] : 'y');
	$smarty->assign('showyourgroups', isset($module_params['showyourgroups']) ? $module_params['showyourgroups'] : 'y');
	$smarty->assign('tpl_module_title', tra("Emulate Groups"));
}
