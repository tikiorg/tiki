<?php
include_once('tiki-setup.php');
if($feature_chat!='y') {
  die;  
}
if($tiki_p_chat!='y') {
  die;  
}
// get a list of messages to display from messages
// use get_messages($lastMessage)
// display the messages (keep the biggest id)
// set lastMessage in the session
//refresh_user($user);
// :TODO: use a preference here instead of 10 minutes
$tikilib->purge_messages(10); 
if(isset($_REQUEST["refresh"])) {
  $refresh = $_REQUEST["refresh"];
} else {
  $refresh = 10000;
}
if(!isset($_SESSION["lastMessage"])) {
  $lastMessage = 0;
} else {
  $lastMessage = $_SESSION["lastMessage"];
}
if(!$lastMessage) $lastMessage=0;
print('<head>');
//prune_users();
//update_channel_ratio($channelId);
if(isset($_REQUEST["channelId"])) {
  $messages = $tikilib->get_messages($_REQUEST["channelId"],$lastMessage,$_REQUEST["enterTime"]); 
  if(count($messages)>0) {
    print("<script>");
    foreach ($messages as $msg) {
      if($msg["poster"]==$_REQUEST["nickname"]) {
        $classt='chuser'; 
      } else {
        $classt='chnormal'; 
      }
      $parsed = $tikilib->parse_comment_data($msg["data"]);
      $prmsg="<span class=\"$classt\">".$msg["posterName"].": ".$parsed."</font><br/>";
      //$com = "top.document.frames[0].document.write('".$prmsg."');";
      $com = "top.chatdata.document.write('".$prmsg."');";
      //$com="top.document.frames[0].document.write('hey')";
      if($msg["messageId"]>$lastMessage) $lastMessage=$msg["messageId"];
      //print("alert('$com');");
      print($com);
      //print("top.document.frames[0].document.write('\n');");
    }
    print("top.chatdata.scrollTo(0,100000)");
    print("</script>");
    session_register("lastMessage");
  }
}

?>
</head>
<?
print('<body onLoad="window.setInterval(\'location.reload()\','.$refresh.');">');
?>
<?
if(isset($com)) {
//print_r($messages);
//print($com);
}
?>
</body>
