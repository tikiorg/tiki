<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $tikilib, $smarty, $tiki_p_admin;

$smarty->assign('groups_are_emulated', $_SESSION["groups_are_emulated"]);
$smarty->assign_by_ref('groups_emulated', unserialize($_SESSION['groups_emulated']));

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
// If group Anonymous was absent, still add it so it is displayed as a reminder that its perms apply
if( !$userGroups["Anonymous"] ) {
	$userGroups["Anonymous"] = "included";
}
$chooseGroups = $userGroups;
$chooseGroups["Anonymous"] = "included";
if(isset($user)) {
	$chooseGroups["Registered"] = "included";
}
$smarty->assign_by_ref('userGroups', $userGroups);
$smarty->assign_by_ref('chooseGroups', $chooseGroups);

