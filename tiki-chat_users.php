<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-chat_users.php,v 1.2 2003-01-04 19:34:16 rossta Exp $

require_once("tiki-setup.php");
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
$chatusers = $tikilib->get_chat_users($_REQUEST["channelId"]);
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