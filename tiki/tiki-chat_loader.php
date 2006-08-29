<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-chat_loader.php,v 1.18 2006-08-29 20:19:02 sylvieg Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$section = 'chat';
include_once ('tiki-setup.php');

include_once ('lib/chat/chatlib.php');

if ($feature_chat != 'y') {
	die;
}

if ($tiki_p_chat != 'y') {
	die;
}

// get a list of messages to display from messages
// use get_messages($lastMessage)
// display the messages (keep the biggest id)
// set lastMessage in the session
//refresh_user($user);
// :TODO: use a preference here instead of 1440 minutes = 1 day
$chatlib->purge_messages(1440);
$chatlib->purge_private_messages($_REQUEST["nickname"], 1440);

if (isset($_REQUEST["refresh"])) {
	$refresh = $_REQUEST["refresh"];
} else {
	$refresh = 10000;
}

if (!isset($_SESSION["lastMessage"])) {
	$lastMessage = 0;
} else {
	$lastMessage = $_SESSION["lastMessage"];
}

if (!$lastMessage)
	$lastMessage = 0;

print ('<html><head>');
print ('
<script type="text/javascript">

function chatdata_setup()
{
	self.scrollTo(0,10000);
	window.setInterval(\'location.reload()\',' . $refresh . ');
}
</script>
');
print ('</head>');

print ('<body onload="chatdata_setup()">');

//prune_users();
//update_channel_ratio($channelId);
if (isset($_REQUEST["channelId"])) {
	$messages = $chatlib->get_messages($_REQUEST["channelId"], 0, 0);

	if (count($messages) > 0) {
		//print ("<script type='text/javascript'>");

		foreach ($messages as $msg) {
			if ($msg["poster"] != $_REQUEST["nickname"]) {
				$classt = 'black';
			} else {
				$classt = 'blue';
			}
			//the order seems to be imported to parse the smile and special characteres
			$parsed = $chatlib->parse_chat_data(htmlspecialchars($msg["data"]));
			$prmsg = "<span style=\"color:$classt;\">" . $msg["posterName"] . ": " . $parsed . "</span><br />";
			//$com = "top.document.frames[0].document.write('".$prmsg."');";
			//$com = "top.chatdata.document.write('" . $prmsg . "');";
			$com = "$prmsg";

			//$com="top.document.frames[0].document.write('hey')";
			if ($msg["messageId"] > $_SESSION["lastMessage"])
				$_SESSION["lastMessage"] = $msg["messageId"];

			//print("alert('$com');");
			print ($com);
			//print("top.document.frames[0].document.write('\n');");
		}

		//print ("top.chatdata.scrollTo(0,100000)");
		//print ("</script>");
	//session_register("lastMessage");
	}
}

$messages = $chatlib->get_private_messages($_REQUEST["nickname"]);

if (count($messages) > 0) {
	//print ("<script type='text/javascript'>");

	foreach ($messages as $msg) {
		$classt = 'red';

		$parsed = $chatlib->parse_chat_data($msg["data"]);
		$prmsg = "<span style=\"color:$classt;\">" . $msg["posterName"] . ": " . $parsed . "</span><br />";
		//$com = "top.chatdata.document.write('" . $prmsg . "');";
		$com = "$prmsg";
		print ($com);
	}

	//print ("top.chatdata.scrollTo(0,100000)");
	//print ("</script>");
}

//if (isset($com)) {
//print_r($messages);
//print(htmlentities($com));
//}

print ('</body></html>');

?>
