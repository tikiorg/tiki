<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/chat/chatlib.php');

if($feature_chat!='y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
if($tiki_p_chat!='y') {
  $smarty->assign('msg',tra("Permission denied to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
$channels = $chatlib->list_active_channels(0,-1,'name_desc','');
$smarty->assign('channels',$channels["data"]);

$section='chat';
include_once('tiki-section_options.php');


// Display the template
$smarty->assign('mid','tiki-chat.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>