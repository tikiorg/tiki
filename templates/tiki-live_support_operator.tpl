<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/{$style}" type="text/css" />
    {include file="bidi.tpl"}
    <title>Live support:Operator Window</title>
    {literal}
	<script type="text/javascript" src="lib/live_support/live-support.js">
	</script>
	{/literal}
	{$trl}
  </head>
  <body onUnload="javascript:operator_close();">
  	<input type="hidden" id="reqId" value="{$reqId}" />
  	<input type="hidden" id="senderId" value="{$senderId}" />
  	<input type="hidden" id="username" value="{$user}" />
  	Operator window: {$senderId} in {$reqId}<br/>
  	<iframe name='chat_data' src='tiki-live_support_chat_frame.php' width="290" height="300" scrolling="yes">
  	</iframe>
  	<input type="text" id="data" />
  	<input type="button" name="send" onClick="javascript:write_msg(document.getElementById('data').value,'operator',document.getElementById('username').value);" />
  	<script>
        /* Activate polling of requests */
        var last_event=0;
        event_poll();
    </script>
  </body>
</html>  