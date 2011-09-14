<!DOCTYPE html>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/{$prefs.style}" type="text/css" />
    {include file='bidi.tpl'}
    <title>Live support:{$role} window</title>
    {literal}
	<script type="text/javascript" src="lib/live_support/live-support.js">
	</script>
	{/literal}
	{$headerlib->output_headers()}
  </head>
  <body onunload="javascript:chat_close(document.getElementById('role').value,document.getElementById('username').value);">
  	<input type="hidden" id="reqId" value="{$reqId|escape}" />
  	<input type="hidden" id="senderId" value="{$senderId|escape}" />
  	<input type="hidden" id="role" value="{$role|escape}" />
	{if $role eq 'user'}
	{if $req_info.tiki_user}
	  	<input type="hidden" id="username" value="{$req_info.tiki_user|escape}" />
	{else}
		<input type="hidden" id="username" value="{$req_info.user|escape}" />
	{/if}
		<table>
			<tr>
				<td  valign="top" style="text-align:center;">{$req_info.operator|avatarize}<br />
					<b>{$req_info.operator}</b>
				</td>
				<td valign="top" >
					{tr}Chat started{/tr}<br />
					<i>{$req_info.reason}</i>
				</td>
			</tr>
		</table>
	{elseif $role eq 'operator'}
	  	<input type="hidden" id="username" value="{$req_info.operator|escape}" />

		{if $req_info.tiki_user}
			<table>
			<tr>
				<td  valign="top" style="text-align:center;">{$req_info.tiki_user|avatarize}<br />
					<b>{$req_info.tiki_user}</b>({$IP})
				</td>
				<td valign="top" >
					{tr}Chat started{/tr}<br />
					<i>{$req_info.reason}</i>
				</td>
			</tr>
			</table>
		{else}
			<table>
			<tr>
				<td valign="top" style="text-align:center;">
					<b>{$req_info.user}</b>({$IP})
				</td>
				<td valign="top" >
					{tr}Chat started{/tr}<br />
					<i>{$req_info.reason}</i>
				</td>
			</tr>
			</table>
		{/if}
	{else}
		<table >
			<tr>
				<td  style="text-align:center;" valign="top">
					<b>{tr}User:{/tr}</b><br />
					{if $req_info.tiki_user}
						{$req_info.tiki_user|avatarize}<br />
						<b>{$req_info.tiki_user}</b>
					{else}
						<b>{$req_info.user}</b>
					{/if}
				</td>
				<td valign="top">
					<i>{$req_info.reason}</i>
				</td>
				<td  style="text-align:center;" valign="top">
					<b>{tr}Operator:{/tr}</b><br />
					{$req_info.operator|avatarize}<br />
					<b>{$req_info.operator}</b>				
				</td>
			</tr>
		</table>
	{/if}
  	<iframe name='chat_data' src='tiki-live_support_chat_frame.php' width="290" height="300" scrolling="yes">
  	</iframe>
  	{literal}
  	<input type="text" id="data" size="30" width="290" style="width:290px;" onKeyPress="javascript:if(event.keyCode == 13) {write_msg(document.getElementById('data').value,document.getElementById('role').value,document.getElementById('username').value);}" />
	<br/>
  	<input type="button" value="send" onclick="javascript:write_msg(document.getElementById('data').value,document.getElementById('role').value,document.getElementById('username').value);" />
  	{/literal}
  	<script type='text/javascript'>
        /* Activate polling of requests */
        var last_event=0;
        event_poll();
    </script>
  </body>
</html>  
