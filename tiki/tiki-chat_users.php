<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-chat_users.php,v 1.4 2003-08-06 15:25:19 rossta Exp $

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
?>
	<tr>
		<td valign='top' class='chatchannels'>
  			<?php echo $achatuser["nickname"] ?>
  		</td>
  	/tr>
<?
}
}
?>
</table>
</body>
</html>
