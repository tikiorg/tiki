<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-g-admin_activities.php,v 1.19 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/Galaxia/ProcessManager.php');

$maxExpirationTime = array (
"years" => 5,
"months" => 11,
"days" => 30,
"hours" => 23,
"minutes" => 59
);

// The galaxia activities manager PHP script.
if ($prefs['feature_workflow'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_workflow");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_workflow != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST['pid'])) {
	$smarty->assign('msg', tra("No process indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('pid', $_REQUEST['pid']);

$proc_info = $processManager->get_process($_REQUEST['pid']);
$proc_info['graph']="lib/Galaxia/processes/".$proc_info['normalized_name']."/graph/".$proc_info['normalized_name'].".png";



// Retrieve activity info if we are editing, assign to 
// default values when creating a new activity
if (!isset($_REQUEST['activityId']))
	$_REQUEST['activityId'] = 0;

if ($_REQUEST["activityId"]) {
	$info = $activityManager->get_activity($_REQUEST['pid'], $_REQUEST["activityId"]);
	$time = $activityManager->get_expiration_members($info['expirationTime']);
	$info['year'] = $time['year'];
	$info['month'] = $time['month'];
	$info['day'] = $time['day'];
	$info['hour'] = $time['hour'];
	$info['minute'] = $time['minute'];
} else {
	$info = array(
		'name' => '',
		'description' => '',
		'activityId' => 0,
		'isInteractive' => 'y',
		'isAutoRouted' => 'n',
		'type' => 'activity',
		'month'=> 0,
		'day'=> 0,
		'hour'=> 0,
		'minute'=> 0,
		'expirationTime'=> 0
	);
}

$smarty->assign('activityId', $_REQUEST['activityId']);
$smarty->assign('info', $info);

// Remove a role from the activity
if (isset($_REQUEST['remove_role']) && $_REQUEST['activityId']) {
  $area = 'delgalaxiactivityrole';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$activityManager->remove_activity_role($_REQUEST['activityId'], $_REQUEST['remove_role']);
  } else {
    key_get($area);
  }
}

$role_to_add = 0;

// Add a role to the process
if (isset($_REQUEST['addrole'])) {
	check_ticket('g-admin-activities');
	$isInteractive = (isset($_REQUEST['isInteractive']) && $_REQUEST['isInteractive'] == 'on') ? 'y' : 'n';

	$isAutoRouted = (isset($_REQUEST['isAutoRouted']) && $_REQUEST['isAutoRouted'] == 'on') ? 'y' : 'n';
	$info = array(
		'name' => $_REQUEST['name'],
		'description' => $_REQUEST['description'],
		'activityId' => $_REQUEST['activityId'],
		'isInteractive' => $isInteractive,
		'isAutoRouted' => $isAutoRouted,
		'type' => $_REQUEST['type'],
		'month'=> 0,
		'day'=> 0,
		'hour'=> 0,
		'minute'=> 0,
		'expirationTime'=> 0
	);

	if (empty($_REQUEST['rolename'])) {
		$smarty->assign('msg', tra("Role name cannot be empty"));

		$smarty->display("error.tpl");
		die;
	}

	$vars = array(
		'name' => $_REQUEST['rolename'],
		'description' => ''
	);

	if (isset($_REQUEST["userole"]) && $_REQUEST["userole"]) {
		if ($_REQUEST['activityId']) {
			$activityManager->add_activity_role($_REQUEST['activityId'], $_REQUEST["userole"]);
		}
	} else {
		$rid = $roleManager->replace_role($_REQUEST['pid'], 0, $vars);

		if ($_REQUEST['activityId']) {
			$activityManager->add_activity_role($_REQUEST['activityId'], $rid);
		}
	}
}

// Delete activities
if (isset($_REQUEST["delete_act"])) {
	check_ticket('g-admin-activities');
	foreach (array_keys($_REQUEST["activity"])as $item) {
		$activityManager->remove_activity($_REQUEST['pid'], $item);
	}
}

// If we are adding an activity then add it!
if (isset($_REQUEST['save_act'])) {
	check_ticket('g-admin-activities');
	$isInteractive = (isset($_REQUEST['isInteractive']) && $_REQUEST['isInteractive'] == 'on') ? 'y' : 'n';

	$isAutoRouted = (isset($_REQUEST['isAutoRouted']) && $_REQUEST['isAutoRouted'] == 'on') ? 'y' : 'n';
	$vars = array(
		'name' => $_REQUEST['name'],
		'description' => $_REQUEST['description'],
		'activityId' => $_REQUEST['activityId'],
		'isInteractive' => $isInteractive,
		'isAutoRouted' => $isAutoRouted,
		'type' => $_REQUEST['type'],
		'expirationTime' => $_REQUEST['year']*535680+$_REQUEST['month']*44640+$_REQUEST['day']*1440+$_REQUEST['hour']*60+$_REQUEST['minute']
	);

	if (empty($_REQUEST['name'])) {
		$smarty->assign('msg', tra("Activity name cannot be empty"));

		$smarty->display("error.tpl");
		die;
	}

	if ($activityManager->activity_name_exists($_REQUEST['pid'], $_REQUEST['name']) && $_REQUEST['activityId'] == 0) {
		$smarty->assign('msg', tra("Activity name already exists"));

		$smarty->display("error.tpl");
		die;
	}

	$newaid = $activityManager->replace_activity($_REQUEST['pid'], $_REQUEST['activityId'], $vars);
	$rid = 0;

	if (isset($_REQUEST['userole']) && $_REQUEST['userole'])
		$rid = $_REQUEST['userole'];

	if (!empty($_REQUEST['rolename'])) {
		$vars = array(
			'name' => $_REQUEST['rolename'],
			'description' => ''
		);

		$rid = $roleManager->replace_role($_REQUEST['pid'], 0, $vars);
	}

	if ($rid) {
		$activityManager->add_activity_role($newaid, $rid);
	}

	$info = array(
		'name' => '',
		'description' => '',
		'activityId' => 0,
		'isInteractive' => 'y',
		'isAutoRouted' => 'n',
		'type' => 'activity'
	);

	$_REQUEST['activityId'] = 0;
	$smarty->assign('info', $info);
	// remove transitions
	$activityManager->remove_activity_transitions($_REQUEST['pid'], $newaid);

	if (isset($_REQUEST["add_tran_from"])) {
		foreach ($_REQUEST["add_tran_from"] as $actfrom) {
			$activityManager->add_transition($_REQUEST['pid'], $actfrom, $newaid);
		}
	}

	if (isset($_REQUEST["add_tran_to"])) {
		foreach ($_REQUEST["add_tran_to"] as $actto) {
			$activityManager->add_transition($_REQUEST['pid'], $newaid, $actto);
		}
	}
}

// Get all the process roles
$all_roles = $roleManager->list_roles($_REQUEST['pid'], 0, -1, 'name_asc', '');
$smarty->assign_by_ref('all_roles', $all_roles['data']);

// Get activity roles
if ($_REQUEST['activityId']) {
	$roles = $activityManager->get_activity_roles($_REQUEST['activityId']);
} else {
	$roles = array();
}

$smarty->assign('roles', $roles);

$where = '';

if (isset($_REQUEST['filter'])) {
	$wheres = array();

	if ($_REQUEST['filter_type']) {
		$wheres[] = " type='" . $_REQUEST['filter_type'] . "'";
	}

	if ($_REQUEST['filter_interactive']) {
		$wheres[] = " isInteractive='" . $_REQUEST['filter_interactive'] . "'";
	}

	if ($_REQUEST['filter_autoroute']) {
		$wheres[] = " isAutoRouted='" . $_REQUEST['filter_autoroute'] . "'";
	}

	if ($_REQUEST['filter_role']) {
		$wheres[] = " ga.activityId = gar.activityId AND gar.roleId=" . $_REQUEST['filter_role'];
	}

	$where = implode('AND', $wheres);
}

if (!isset($_REQUEST['sort_mode']))
	$_REQUEST['sort_mode'] = 'flowNum_asc';

if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';

if (!isset($_REQUEST['were']))
	$_REQUEST['where'] = $where;

$smarty->assign('sort_mode', $_REQUEST['sort_mode']);
$smarty->assign('find', $_REQUEST['find']);
$smarty->assign('where', $_REQUEST['where']);

// Transitions
if (isset($_REQUEST["delete_tran"])) {
	check_ticket('g-admin-activities');
	foreach (array_keys($_REQUEST["transition"])as $item) {
		$parts = explode("_", $item);

		$activityManager->remove_transition($parts[0], $parts[1]);
	}
}

if (isset($_REQUEST['add_trans'])) {
	check_ticket('g-admin-activities');
	$activityManager->add_transition($_REQUEST['pid'], $_REQUEST['actFromId'], $_REQUEST['actToId']);
}

if (isset($_REQUEST['filter_tran_name']) && $_REQUEST['filter_tran_name']) {
	$transitions = $activityManager->get_process_transitions($_REQUEST['pid'], $_REQUEST['filter_tran_name']);
} else {
	$transitions = $activityManager->get_process_transitions($_REQUEST['pid'], '');
}

if (!isset($_REQUEST['filter_tran_name']))
	$_REQUEST['filter_tran_name'] = '';

$smarty->assign('filter_tran_name', $_REQUEST['filter_tran_name']);
$smarty->assign_by_ref('transitions', $transitions);

$valid = $activityManager->validate_process_activities($_REQUEST['pid']);
$proc_info['isValid'] = $valid ? 'y' : 'n';

if ($valid && isset($_REQUEST['activate_proc'])) {
	check_ticket('g-admin-activities');
	$processManager->activate_process($_REQUEST['pid']);

	$proc_info['isActive'] = 'y';
}

if (isset($_REQUEST['deactivate_proc'])) {
	check_ticket('g-admin-activities');
	$processManager->deactivate_process($_REQUEST['pid']);

	$proc_info['isActive'] = 'n';
}

$smarty->assign_by_ref('proc_info', $proc_info);

$errors = array();

if (!$valid) {
	$errors = $activityManager->get_error();
}

$smarty->assign('errors', $errors);

//Now information for activities in this process
$activities = $activityManager->list_activities($_REQUEST['pid'], 0, -1, $_REQUEST['sort_mode'], $_REQUEST['find'], $where);

//Now check if the activity is or not part of a transition
if (isset($_REQUEST['activityId'])) {
	$temp_max = count($activities["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		$id = $activities["data"][$i]['activityId'];

		$activities["data"][$i]['to']
			= $activityManager->transition_exists($_REQUEST['pid'], $_REQUEST['activityId'], $id) ? 'y' : 'n';
		$activities["data"][$i]['from']
			= $activityManager->transition_exists($_REQUEST['pid'], $id, $_REQUEST['activityId']) ? 'y' : 'n';
	}
}

// Set activities
if (isset($_REQUEST["update_act"])) {
	$temp_max = count($activities["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		$id = $activities["data"][$i]['activityId'];

		if (isset($_REQUEST['activity_inter']["$id"])) {
			$activities["data"][$i]['isInteractive'] = 'y';

			$activityManager->set_interactivity($_REQUEST['pid'], $id, 'y');
		} else {
			$activities["data"][$i]['isInteractive'] = 'n';

			$activityManager->set_interactivity($_REQUEST['pid'], $id, 'n');
		}

		if (isset($_REQUEST['activity_route']["$id"])) {
			$activities["data"][$i]['isAutoRouted'] = 'y';

			$activityManager->set_autorouting($_REQUEST['pid'], $id, 'y');
		} else {
			$activities["data"][$i]['isAutoRouted'] = 'n';

			$activityManager->set_autorouting($_REQUEST['pid'], $id, 'n');
		}
	}
}


$arYears = array ();
$arMonths = array();
$arDays = array();
$arHours = array();
$arminutes = array();
for ($i=0;$i<=$maxExpirationTime['months'];$i++)
	$arMonths[$i] = $i;
for ($i=0;$i<=$maxExpirationTime['years'];$i++)
	$arYears[$i] = $i;
for ($i=0;$i<=$maxExpirationTime['days'];$i++)
	$arDays["$i"] = $i;
for ($i=0;$i<=$maxExpirationTime['hours'];$i++)
	$arHours["$i"] = $i;
for ($i=0;$i<=$maxExpirationTime['minutes'];$i++)
	$arminutes["$i"] = $i;
$smarty->assign("years",$arYears);
$smarty->assign("months",$arMonths);
$smarty->assign("days",$arDays);
$smarty->assign("hours",$arHours);
$smarty->assign("minutes",$arminutes);



$smarty->assign_by_ref('items', $activities['data']);

$activityManager->build_process_graph($_REQUEST['pid']);
ask_ticket('g-admin-activities');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-g-admin_activities.tpl');
$smarty->display("tiki.tpl");

?>
