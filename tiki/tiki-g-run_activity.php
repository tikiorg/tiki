<?php
include('tiki-setup.php');
include('lib/Galaxia/API.php');

$__activity_completed = false;

if($feature_workflow != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_use_workflow != 'y') {
  $smarty->assign('msg',tra("Permission denied"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
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



// This goes to the end part of all activities
// If this activity is interactive then we have to display the template

if($__activity_completed ) {

	$smarty->assign('procname',$process->getName());
	$smarty->assign('procversion',$process->getVersion());
	$smarty->assign('actname',$activity->getName());
	$smarty->assign('mid','tiki-g-activity_completed.tpl');
	$smarty->display("styles/$style_base/tiki.tpl");
	
} else {
	if($activity->isInteractive) {
		$section='workflow';
		include_once('tiki-section_options.php');
	 	$template = $activity->getNormalizedName().'.tpl';
		$smarty->assign('mid',$process->getNormalizedName().'/'.$template);
		$smarty->display("styles/$style_base/tiki.tpl");
	}
}

?>