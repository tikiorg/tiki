{title help="Inter-User Messages"}{tr}Compose message{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
{include file='messu-nav.tpl'}

{if $allowMsgs ne 'y'}<br />
<div class="simplebox">{icon _id=information style="vertical-align:middle" align="left"} {tr}If you want people to be able to reply to you, enable <a href='tiki-user_preferences.php'>Allow messages from other users</a> in your preferences.{/tr}</div><br /><br />
{/if}


{if $sent}
<div class="simplebox highlight">{if (strstr($message, "{tr}ERROR{/tr}")) or (strstr($message, "{tr}Invalid{/tr}"))}{icon _id=delete alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"}{else}{icon _id=accept alt="{tr}Send{/tr}" style="vertical-align:middle"} {/if}{$message}</div>
{/if}

{if (!$sent) or ((strstr($message, "{tr}ERROR{/tr}")) or (strstr($message, "{tr}Invalid{/tr}")))}
{jq}$jq(".username").tiki("autocomplete", "username", {multiple: true, multipleSeparator: ";"});{/jq}
<form action="messu-compose.php" method="post">
<table class="normal" >
  <tr>
    <td class="formcolor"><label for="mess-composeto">{tr}To:{/tr}</label>
    	{help url="Inter-User+Messages#Composing_messages" desc="{tr}To{/tr}:{tr}Multiple addresses can be separated with semicolons (\";\"){/tr}"}
    </td><td class="formcolor"><input type="text" name="to" id="mess-composeto" value="{$to|escape}" class="username" />
		<input type="hidden" name="replyto_hash" value="{$replyto_hash}" />
		<input type="hidden" name="reply" value="{$reply}" />
</td>
  </tr>
  <tr>
    <td class="formcolor"><label for="mess-composecc">{tr}CC:{/tr}</label>
    	{help url="Inter-User+Messages#Composing_messages" desc="{tr}CC{/tr}:{tr}Multiple addresses can be separated with semicolons (\";\"){/tr}"}
    </td><td class="formcolor"><input type="text" name="cc" id="mess-composecc" value="{$cc|escape}" class="username" /></td>
  </tr>
  <tr>
    <td class="formcolor"><label for="mess-composebcc">{tr}BCC:{/tr}</label>
    	{help url="Inter-User+Messages#Composing_messages" desc="{tr}CC{/tr}:{tr}Multiple addresses can be separated with semicolons (\";\"){/tr}"}
    </td><td class="formcolor"><input type="text" name="bcc" id="mess-composebcc" value="{$bcc|escape}" class="username" /> </td>
  </tr>
  <tr>
    <td class="formcolor"><label for="mess-prio">{tr}Priority:{/tr}</label></td><td class="formcolor">
    <select name="priority" id="mess-prio">
      <option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
      <option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
      <option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
      <option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
      <option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
    </select>
    </td>
  </tr>
  <tr>
    <td class="formcolor"><label for="mess-subj">{tr}Subject:{/tr}</label></td><td class="formcolor"><input type="text" name="subject" id="mess-subj" value="{$subject|escape}" size="80" maxlength="255"/></td>
  </tr>
</table>
<br />
<table class="normal" >
  <tr>
    <td style="text-align: center;" class="formcolor"><textarea rows="20" cols="80" name="body">{$body|escape}</textarea></td>
  </tr>
	<tr><td><input type="submit" name="send" value="{tr}Send{/tr}" /></td></tr>
  </table>
</form>
{/if}
<br />
