<?php
//Code to be executed before an activity
// If we didn't retrieve the instance before
if(empty($instance->instanceId)) {
  // This activity needs an instance to be passed to 
  // be started, so get the instance into $instance.
  if(isset($_REQUEST['iid'])) {
    $instance->getInstance($_REQUEST['iid']);
  } else {
    // defined in lib/Galaxia/config.php
    galaxia_show_error("No instance indicated");
    die;  
  }
}
if ($instance->getActivityStatus($_REQUEST['activityId']) == "completed")
{
	$smarty->assign("msg",tra("This instance of activity is already complete"));
	$smarty->display("error.tpl");
	die;
}

// Set the current user for this activity
if(isset($user) && !empty($instance->instanceId) && !empty($activity->activityId)) {
  $instance->setActivityUser($activity->activityId,$user);
}
?>
