<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function module_groups_emulation_info() {
	return array(
		'name' => tra('Groups emulation'),
		'description' => tra('Enables temporarily changing one\'s group memberships to see how users in fewer groups experience the site.'),
		'prefs' => array(),
		'params' => array(),
		'common_params' => array('rows')
		
	);
}

function module_groups_emulation( $mod_reference, $module_params ) {
	global $smarty, $user, $tiki_p_admin, $userlib;
	
	$smarty->assign('groups_are_emulated', isset($_SESSION['groups_are_emulated']) ? $_SESSION['groups_are_emulated'] : 'n');
	if (isset($_SESSION['groups_emulated']))
		$smarty->assign('groups_emulated', unserialize($_SESSION['groups_emulated']));
	
	// Admins can see all existing groups
	if ($tiki_p_admin == 'y') {
		$allGroups = array();
		$alls = $userlib->get_groups();
		foreach($alls['data'] as $g) {
			$allGroups[$g['groupName']] = "real";
		}
		$smarty->assign_by_ref('allGroups', $allGroups);
	}
	
	// Extract list of groups of user, including included groups
	$userGroups = $userlib->get_user_groups_inclusion($user);
	$chooseGroups = $userGroups;
	$chooseGroups["Anonymous"] = "included";
	if(isset($user)) {
		$chooseGroups["Registered"] = "included";
	}
	$smarty->assign_by_ref('userGroups', $userGroups);
	$smarty->assign_by_ref('chooseGroups', $chooseGroups);
	$smarty->assign('tpl_module_title', tra("Emulate Groups"));
}
