<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/{$style}" type="text/css" />
    {include file="bidi.tpl"}
    <title>Live support:User window</title>
    {literal}
	<script type="text/javascript" src="lib/live_support/live-support.js">
	</script>
	{/literal}
	{$trl}
  </head>
  <body onUnload="javascript:client_close();">
  	<div id='request_chat'>
		This is the client window<br/>
		<input type="hidden" id="reqId" />
		{if $user}
			User: {$user} ({$user_email})<br/>
			<input type="hidden" id="user" value="{$user}" />
			<input type="hidden" id="email" value="{$user_email}" />
			<input type="hidden" id="tiki_user" value="{$user}" />
		{else}
			User <input type="text" id="user" />
			Email <input type="text" id="email" />
			<input type="hidden" id="tiki_user" value="" />
		{/if}
		Reason: <input id='reason' type="text" />
		<input onClick="request_chat(document.getElementById('user').value,document.getElementById('tiki_user').value,document.getElementById('email').value,document.getElementById('reason').value);" type="button" value="send" />
	</div>
	
	<div id='requesting_chat' style='display:none;'>
		Requesting chat
	</div>
	
	<div id='chat' style='display:none;'>
		User window {$senderId}<br/>
	  	<input type="hidden" id="senderId" value="{$senderId}" />
	  	<input type="hidden" id="username" />
  		<iframe name='chat_data' src='tiki-live_support_chat_frame.php' width="290" height="300" scrolling="yes">
  		</iframe>
  		<input type="text" id="data" />
  		<input type="button" name="send" onClick="javascript:write_msg(document.getElementById('data').value,'user',document.getElementById('username').value);" />
  		<script>
        	/* Activate polling of requests */
        	var last_event=0;
        	
    	</script>

	</div>
  </body>
</html>  