<?php
// Initialization
require_once('tiki-setup.php');
if($feature_chat!='y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
if($tiki_p_chat!='y') {
  $smarty->assign('msg',tra("Permission denied to use this feature"));
  $smarty->display('error.tpl');
  die;  
}
$channels = $tikilib->list_active_channels(0,-1,'name_desc','');
$smarty->assign('channels',$channels["data"]);

// Display the template
$smarty->assign('mid','tiki-chat.tpl');
$smarty->display('tiki.tpl');
?>