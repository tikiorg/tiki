<?php
//Code to be executed before a start activity
// Create: create a new instance
if(isset($_REQUEST['iid'])) {
  $instance->getInstance($_REQUEST['iid']);
} 

?>
