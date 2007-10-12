<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-g-run_activity.php,v 1.19 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include ('tiki-setup.php');

include ('lib/Galaxia/API.php');
include_once ("lib/webmail/htmlMimeMail.php");

$__activity_completed = false;

if ($prefs['feature_workflow'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_workflow");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST['auto'])) {
	if ($tiki_p_use_workflow != 'y') {
		$smarty->assign('msg', tra("Permission denied"));

		$smarty->display("error.tpl");
		die;
	}
}

// Determine the activity using the activityId request
// parameter and get the activity information
// load then the compiled version of the activity
if (!isset($_REQUEST['activityId'])) {
	$smarty->assign('msg', tra("No activity indicated"));

	$smarty->display("error.tpl");
	die;
}

// When $user is null, it means an anonymous user is trying to use Galaxia
$user = is_null($user) ? "Anonymous" : $user;

$activity = $baseActivity->getActivity($_REQUEST['activityId']);
$process->getProcess($activity->getProcessId());

// Get user roles

// Get activity roles
$act_roles = $activity->getRoles();
$user_roles = $activity->getUserRoles($user);

// Only run a start activity if a name is set for the instance
if ($activity->getType() == 'start') {
	if (!isset($_REQUEST['name'])) {
		$smarty->assign('msg', tra("A start activity requires a name for the instance"));

		$smarty->display("error.tpl");
		die;
	}
}

// Only check roles if this is an interactive
// activity
if ($activity->isInteractive() == 'y') {
	if (!count(array_intersect($act_roles, $user_roles))) {
		$smarty->assign('msg', tra("You cant execute this activity"));

		$smarty->display("error.tpl");
		die;
	}
}

$act_role_names = $activity->getActivityRoleNames($user);

foreach ($act_role_names as $role) {
	$name = 'tiki-role-' . $role['name'];

	if (in_array($role['roleId'], $user_roles)) {
		$smarty->assign("$name", 'y');

		$$name = 'y';
	} else {
		$smarty->assign("$name", 'n');

		$$name = 'n';
	}
}

if (!isset($_REQUEST['__post'])) {
	// Existing variables here:
	// $process, $activity, $instance (if not standalone)

	// Get paths for original and compiled activity code
	$origcode = 'lib/Galaxia/processes/' . $process->getNormalizedName(). '/code/activities/' . $activity->getNormalizedName() . '.php';
	$compcode = 'lib/Galaxia/processes/' . $process->getNormalizedName() . '/compiled/' . $activity->getNormalizedName(). '.php';

	// Now get paths for original and compiled template
	$origtmpl = 'lib/Galaxia/processes/' . $process->getNormalizedName(). '/code/templates/'  . $activity->getNormalizedName() . '.tpl';
	$comptmpl = 'templates/' . $process->getNormalizedName() . '/' . $activity->getNormalizedName(). '.tpl';

	// Check whether the activity code or template are newer than their compiled counterparts,
	// i.e. check if the source code or template were changed; if so, we need to recompile
	if ((filemtime($origcode) > filemtime($compcode)) or (filemtime($origtmpl) > filemtime($comptmpl))) {
		// Recompile the activity
		include_once ('lib/Galaxia/ProcessManager.php');
		$activityManager->compile_activity($activity->getProcessId(), $activity->getActivityId());
	}

	// Get paths for shared code and activity
	$shared = 'lib/Galaxia/processes/' . $process->getNormalizedName(). '/code/shared.php';
	$source = $compcode;

	// Include the shared code
	include_once ($shared);

	// Now do whatever you have to do in the activity
	include_once ($source);
}

// Process comments
if (isset($_REQUEST['__removecomment'])) {
	$__comment = $instance->get_instance_comment($_REQUEST['__removecomment']);

	if ($__comment['user'] == $user or $tiki_p_admin_workflow == 'y') {
		$area = "delinstancecomment";
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$instance->remove_instance_comment($_REQUEST['__removecomment']);
		} else {
			key_get($area);
		}
	}
}

$smarty->assign_by_ref('__comments', $__comments);

if (!isset($_REQUEST['__cid']))
	$_REQUEST['__cid'] = 0;
if (isset($_REQUEST['__post'])) {
	$instance->getInstance($_REQUEST['iid']);
	$instance->replace_instance_comment($_REQUEST['__cid'], $activity->getActivityId(), $activity->getName(),
		$user, $_REQUEST['__title'], $_REQUEST['__comment']);
}
$__comments = $instance->get_instance_comments($activity->getActivityId());
// This goes to the end part of all activities
// If this activity is interactive then we have to display the template
if (!isset($_REQUEST['auto']) && $__activity_completed && $activity->isInteractive() && !isset($_REQUEST['__post'])) {
	$smarty->assign('procname', $process->getName());

	$smarty->assign('procversion', $process->getVersion());
	$smarty->assign('actname', $activity->getName());
	$smarty->assign('actid',$activity->getActivityId());
	$smarty->assign('post','n');
	$smarty->assign('iid',$instance->instanceId);
	$smarty->assign('mid', 'tiki-g-activity_completed.tpl');
	$smarty->display("tiki.tpl");
} 
elseif (!isset($_REQUEST['auto']) && $activity->isInteractive() && isset($_REQUEST['__post'])) {
	$smarty->assign('procversion', $process->getVersion());
	$smarty->assign('actname', $activity->getName());
	$smarty->assign('actid',$activity->getActivityId());
	$smarty->assign('title',$_REQUEST['__title']);
	$smarty->assign('comment',$_REQUEST['__comment']);
	$smarty->assign('post','y');
	$smarty->assign('mid', 'tiki-g-activity_completed.tpl');
	$smarty->display("tiki.tpl");
}
else {
	if (!isset($_REQUEST['auto']) && $activity->isInteractive()) {
		$section = 'workflow';

		include_once ('tiki-section_options.php');
		$template = $activity->getNormalizedName(). '.tpl';
		$smarty->assign('mid', $process->getNormalizedName(). '/' . $template);
		$smarty->display("tiki.tpl");
	}
}
?>
