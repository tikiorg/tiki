<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-chat_users.php,v 1.3 2003-03-21 18:55:20 lrargerich Exp $

require_once("tiki-setup.php");
include_once('lib/chat/chatlib.php');
?>
<html>
<head>
<?php
print('<link rel="StyleSheet" href="styles/'.$style.'" type="text/css" />'); 
?>
</head>
<body style="margin:0px;" onLoad="window.setInterval('location.reload()','10000');">
<table width="100%" height="100%">
<?php
if(isset($_REQUEST["channelId"])) {
$chatusers = $chatlib->get_chat_users($_REQUEST["channelId"]);
foreach($chatusers as $achatuser) {
  print("<tr><td valign='top' class='chatchannels'>");
  print($achatuser["nickname"]);
  print("</td></tr>");
}
}
?>
</td></tr>
</body>
</html>