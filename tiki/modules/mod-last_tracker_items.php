<?php
if($feature_trackers == 'y') {

$smarty->assign('modlifn',$module_params["name"]);
if(isset($module_params["trackerId"])) {
  $ranking = $tikilib->list_tracker_items($module_params["trackerId"],0,$module_rows,'created_desc','');
} else {
  $ranking = array();
}
$smarty->assign('modLastItems',$ranking["data"]);
}
?>