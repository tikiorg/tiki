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
if(!isset($_REQUEST["channelId"])) {
    $smarty->assign('msg',tra("No channel indicated"));
    $smarty->display('error.tpl');
    die;
}
$channelId=$_REQUEST["channelId"];
//session_register('channelId');


if($user) {
  $nickname = $user;
} else {
  if(!isset($_REQUEST["nickname"]) || empty($_REQUEST["nickname"])) {
    $smarty->assign('msg',tra("No nickname indicated"));
    $smarty->display('error.tpl');
    die;
  }
  $nickname = $_REQUEST["nickname"];
}



//session_register("nickname");
$enterTime = date("U");
//session_register('enterTime');
if($tiki_p_admin_chat == 'y') {
  $nickname = '@'.$nickname;
}
$tikilib->user_to_channel($nickname,$channelId);
$smarty->assign('nickname',$nickname);
$smarty->assign('channelId',$_REQUEST["channelId"]);
$smarty->assign('now',date("U"));
$info = $tikilib->get_channel($_REQUEST["channelId"]);
$refresh=$info["refresh"];
$name=$info["name"];
//session_register('refresh');
$smarty->assign('channelName',$name);
$smarty->assign_by_ref('channelInfo',$info);
$smarty->assign('refresh',$refresh);
$channels = $tikilib->list_active_channels(0,-1,'name_desc','');
$smarty->assign_by_ref('channels',$channels["data"]);
$chatusers = $tikilib->get_chat_users($channelId);
$smarty->assign_by_ref('chatusers',$chatusers);

$section='chat';
include_once('tiki-section_options.php');


// Display the template
$smarty->assign('mid','tiki-chatroom.tpl');
$smarty->display('tiki.tpl');
?>