<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/{$prefs.style}" type="text/css" />
    {include file='bidi.tpl'}
    <title>{tr}Live support:User window{/tr}</title>
    {literal}
	<script type="text/javascript" src="lib/live_support/live-support.js">
	</script>
	{/literal}
	{$headerlib->output_headers()}
  </head>
  <body onunload="client_close();">
  	<div id='request_chat' align="center">
  		<input type="hidden" id="reqId" />
		<input type="hidden" id="tiki_user" value="{$user|escape}" />
		
		<h2>{tr}Request live support{/tr}</h2>
		<table>
			{if $user}
				<input type="hidden" id="username" value="{$user|escape}" />
				<input type="hidden" id="emailaddress" value="{$user_email|escape}" />
				<tr>
					<td>{tr}User{/tr}</td>
					<td>
						{$user}				
					</td>
				</tr>
				<tr>
					<td>{tr}Email{/tr}</td>
					<td>
						{$user_email}
					</td>
				</tr>
			{else}
				<tr>
					<td>{tr}User{/tr}</td>
					<td>
						<input type="text" id="username" />			
					</td>
				</tr>
				<tr>
					<td>{tr}Email{/tr}</td>
					<td>
						<input type="text" id="emailaddress" />
					</td>
				</tr>
			{/if}
			<tr>
				<td>{tr}Reason{/tr}</td>
				<td>
					<!--input id='reason' type="text" />-->
					<textarea id='reason' cols='20' rows='3'></textarea>		
				</td>
			</tr>	
		</table>
		
		<br /><br />				
		<input onclick="request_chat(document.getElementById('username').value,document.getElementById('tiki_user').value,document.getElementById('emailaddress').value,document.getElementById('reason').value);" type="button" value="{tr}Request support{/tr}" />
		<br /><br />
		<!--<a href="tiki-live_support_message.php" class="link">{tr}Open a support ticket instead{/tr}</a>-->
	</div>
	
	<div id='requesting_chat' style='display:none;'>
		<b>{tr}Your request is being processed{/tr}....</b>
		<br /><br />
		<a href="javascript:client_close();window.close();" class="link">{tr}cancel request and exit{/tr}</a><br />
		<!--<a href="tiki-live_support_message.php" class="link">{tr}cancel request and leave a message{/tr}</a><br />-->
	</div>
	
  </body>
</html>  
