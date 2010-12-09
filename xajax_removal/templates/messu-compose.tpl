{title help="Inter-User Messages"}{tr}Compose message{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
{include file='messu-nav.tpl'}

{if $allowMsgs ne 'y'}<br />
	<div class="simplebox">
		{icon _id=information style="vertical-align:middle" align="left"} {tr}If you want people to be able to reply to you, enable <a href='tiki-user_preferences.php'>Allow messages from other users</a> in your preferences.{/tr}
	</div>
	<br />
	<br />
{/if}


{if $sent}
	<div class="simplebox highlight">
		{if (strstr($message, "{tr}ERROR{/tr}")) or (strstr($message, "{tr}Invalid{/tr}"))}
			{icon _id=delete alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"}
		{else}
			{icon _id=accept alt="{tr}Send{/tr}" style="vertical-align:middle"}
		{/if}
		{$message}
	</div>
{/if}

{if (!$sent) or ((strstr($message, "{tr}ERROR{/tr}")) or (strstr($message, "{tr}Invalid{/tr}")))}
{if $prefs.user_selector_realnames_messu == 'y'}
{jq}$(".username").tiki("autocomplete", "userrealname", {multiple: true, multipleSeparator: ";"});{/jq}
{else}
{jq}$(".username").tiki("autocomplete", "username", {multiple: true, multipleSeparator: ";"});{/jq}
{/if}
	<form action="messu-compose.php" method="post">
		<table class="formcolor" >
			<tr>
				<td>
					<label for="mess-composeto">{tr}To:{/tr}</label>
					{help url="Inter-User+Messages#Composing_messages" desc="{tr}To{/tr}:{tr}Multiple addresses can be separated with semicolons (\";\"){/tr}"}
				</td>
				<td>
					<input type="text" name="to" id="mess-composeto" value="{$to|escape}" class="username" size="80" />
					<input type="hidden" name="replyto_hash" value="{$replyto_hash}" />
					<input type="hidden" name="reply" value="{$reply}" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="mess-composecc">{tr}CC:{/tr}</label>
					{help url="Inter-User+Messages#Composing_messages" desc="{tr}CC{/tr}:{tr}Multiple addresses can be separated with semicolons (\";\"){/tr}"}
				</td>
				<td>
					<input type="text" name="cc" id="mess-composecc" value="{$cc|escape}" class="username" size="80" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="mess-composebcc">{tr}BCC:{/tr}</label>
					{help url="Inter-User+Messages#Composing_messages" desc="{tr}BCC{/tr}:{tr}Multiple addresses can be separated with semicolons (\";\"){/tr}"}
				</td>
				<td>
					<input type="text" name="bcc" id="mess-composebcc" value="{$bcc|escape}" class="username" size="80" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="mess-prio">{tr}Priority:{/tr}</label>
				</td>
				<td>
					<select name="priority" id="mess-prio">
						<option value="1" {if $priority eq 1}selected="selected"{/if}>1: {tr}Lowest{/tr}</option>
						<option value="2" {if $priority eq 2}selected="selected"{/if}>2: {tr}Low{/tr}</option>
						<option value="3" {if $priority eq 3}selected="selected"{/if}>3: {tr}Normal{/tr}</option>
						<option value="4" {if $priority eq 4}selected="selected"{/if}>4: {tr}High{/tr}</option>
						<option value="5" {if $priority eq 5}selected="selected"{/if}>5: {tr}Very High{/tr}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="mess-subj">{tr}Subject:{/tr}</label>
				</td>
				<td>
					<input type="text" name="subject" id="mess-subj" value="{$subject|escape}" size="80" maxlength="255" />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<textarea rows="20" cols="80" name="body">{$body|escape}</textarea>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="send" value="{tr}Send{/tr}" />
					<input type="checkbox" name="replytome" id="replytome" />
					<label for="replytome">
						{tr}Reply-to my email{/tr}
						{help url="User+Information" desc="{tr}Reply-to my email{/tr}:{tr}The user will be able to reply to you directly via email.{/tr}"}
					</label>
					<input type="checkbox" name="bccme" id="bccme" />
					<label for="bccme">
						{tr}Send me a copy{/tr}
						{help url="User+Information" desc="{tr}Send me a copy{/tr}:{tr}You will be sent a copy of this email.{/tr}"}
					</label>
				</td>
			</tr>
		</table>
	</form>
{/if}
<br />
