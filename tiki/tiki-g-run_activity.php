<?php
include('tiki-setup.php');
include('lib/Galaxia/API.php');
include_once ("lib/webmail/htmlMimeMail.php");

$__activity_completed = false;

if($feature_workflow != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST['auto'])) {
if($tiki_p_use_workflow != 'y') {
  $smarty->assign('msg',tra("Permission denied"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
}


// Determine the activity using the activityId request
// parameter and get the activity information
// load then the compiled version of the activity
if(!isset($_REQUEST['activityId'])) {
  $smarty->assign('msg',tra("No activity indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$activity = $baseActivity->getActivity($_REQUEST['activityId']);
$process->getProcess($activity->getProcessId());

// Get user roles

// Get activity roles
$act_roles = $activity->getRoles();
$user_roles = $activity->getUserRoles($user);
// Only check roles if this is an interactive
// activity
if($activity->isInteractive() == 'y') {
  if(!count(array_intersect($act_roles,$user_roles))) {
    $smarty->assign('msg',tra("You cant execute this activity"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
}
$act_role_names = $activity->getActivityRoleNames($user);
foreach($act_role_names as $role) {
  $name = 'tiki-role-'.$role['name'];
  if(in_array($role['roleId'],$user_roles)) {
    $smarty->assign("$name",'y');
    $$name = 'y';
  } else {
    $smarty->assign("$name",'n');
    $$name = 'n';
  }
 
}


$source = 'lib/Galaxia/processes/'.$process->getNormalizedName().'/compiled/'.$activity->getNormalizedName().'.php';
$shared = 'lib/Galaxia/processes/'.$process->getNormalizedName().'/code/shared.php';

// Existing variables here:
// $process, $activity, $instance (if not standalone)

// Include the shared code
include_once($shared);

// Now do whatever you have to do in the activity
include_once($source);


// Process comments
if(isset($_REQUEST['__removecomment'])) {
  
  $__comment = $instance->get_instance_comment($_REQUEST['__removecomment']);
  if($__comment['user'] == $user or $tiki_p_admin_workflow == 'y') {
    $instance->remove_instance_comment($_REQUEST['__removecomment']);
  }
}
$smarty->assign_by_ref('__comments',$__comments);
if(!isset($_REQUEST['__cid'])) $_REQUEST['__cid']=0;
if(isset($_REQUEST['__post'])) {
  $instance->replace_instance_comment($_REQUEST['__cid'], $activity->getActivityId(), $activity->getName(), $user, $_REQUEST['__title'], $_REQUEST['__comment']);
}
$__comments = $instance->get_instance_comments();


// This goes to the end part of all activities
// If this activity is interactive then we have to display the template
if(!isset($_REQUEST['auto']) && $__activity_completed && $activity->isInteractive()) {
	$smarty->assign('procname',$process->getName());
	$smarty->assign('procversion',$process->getVersion());
	$smarty->assign('actname',$activity->getName());
	$smarty->assign('mid','tiki-g-activity_completed.tpl');
	$smarty->display("styles/$style_base/tiki.tpl");
} else {
	if(!isset($_REQUEST['auto']) && $activity->isInteractive()) {
		$section='workflow';
		include_once('tiki-section_options.php');
	 	$template = $activity->getNormalizedName().'.tpl';
		$smarty->assign('mid',$process->getNormalizedName().'/'.$template);
		$smarty->display("styles/$style_base/tiki.tpl");
	}
}
?>