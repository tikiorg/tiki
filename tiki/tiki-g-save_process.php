<?php
require_once('tiki-setup.php');
include_once('lib/Galaxia/ProcessManager.php');

if($feature_workflow != 'y') {
  die;  
}

if($tiki_p_admin_workflow != 'y') {
  die;  
}


// The galaxia process manager PHP script.

/*
// Check if feature is enabled and permissions
if($feature_galaxia != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
*/

// Check if we are editing an existing process
// if so retrieve the process info and assign it.
if(!isset($_REQUEST['pid'])) $_REQUEST['pid'] = 0;
header('Content-type: text/xml');
echo('<?xml version="1.0"?>');
$data = $processManager->serialize_process($_REQUEST['pid']);
echo $data;
?>