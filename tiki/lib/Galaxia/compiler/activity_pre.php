<?php
//Code to be executed before an activity
// This activity needs an instance to be passed to 
// be started, so get the instance into $instance.
if(isset($_REQUEST['iid'])) {
  $instance->getInstance($_REQUEST['iid']);
} else {
  // defined in lib/Galaxia/config.php
  galaxia_show_error("No instance indicated");
  die;  
}
if(isset($_REQUEST['iid'])&&isset($_REQUEST['activityId'])) {
  $instance->setActivityUser($_REQUEST['activityId'],$user);
}

?>
