<?php
require_once('tiki-setup.php');
include_once('lib/Galaxia/ProcessMonitor.php');

if($feature_workflow != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_admin_workflow != 'y') {
  $smarty->assign('msg',tra("Permission denied"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if(!isset($_REQUEST['itemId'])) {
  $smarty->assign('msg',tra("No item indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$wi = $processMonitor->monitor_get_workitem($_REQUEST['itemId']);
$smarty->assign_by_ref('wi',$wi);

$smarty->assign('stats', $processMonitor->monitor_stats());

$sameurl_elements = Array('offset','sort_mode','where','find','itemId');
$smarty->assign('mid','tiki-g-view_workitem.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>