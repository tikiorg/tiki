<?php
include_once('tiki-setup.php');
if($feature_chat!='y') {
  die;  
}
if($tiki_p_chat!='y') {
  die;  
}
// Check if "send" is set (the user is sending a message)
// check if the channel is moderated if moderated save to moderated messages
// else write the message into messages
// use send_message(userId,channelId,data)
if(isset($_REQUEST["channelId"]) && isset($_REQUEST["nickname"])) {
  if(isset($_REQUEST["data"])) {
    $data = $_REQUEST["data"];
    // Recognize a private message
    if(substr($data,0,1)==':') {
      preg_match("/:([^:]+):(.*)/",$data,$reqs);
      $tikilib->send_private_message($_REQUEST["nickname"],$reqs[1],$reqs[2]);  
    } else {
      if($channelId) {
        $tikilib->send_message($_REQUEST["nickname"],$_REQUEST["channelId"],$data);  
      } 
    }
  }
  $smarty->assign('nickname',$_REQUEST["nickname"]);
  $smarty->assign('channelId',$_REQUEST["channelId"]);
}

// Displaythe box if we are in an active channel
if(isset($_REQUEST["channelId"])) {
  $smarty->display('tiki-chat_box.tpl');
// If not display a message
} else {
  print("no channel selected");
}
?>