{* test *}
<h1><a class="pagetitle" href="messu-compose.php">{tr}Compose message{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Inter-User Messages" target="tikihelp" class="tikihelp" title="{tr}Compose Message{/tr}">
{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-edit_article.tpl" target="tikihelp" class="tikihelp">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>

{include file=tiki-mytiki_bar.tpl}
{include file="messu-nav.tpl"}
<br /><br />

{if $sent}
<div class="simplebox highlight">{if ($message|truncate:5:"":true eq "{tr}ERROR{/tr}") or ($message|truncate:7:"":true eq "{tr}Invalid{/tr}")}{icon _id=delete.png alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"}{else}{icon _id=accept.png alt="{tr}Send{/tr}" style="vertical-align:middle"} {/if}{$message}</div>
{/if}
{if $allowMsgs ne "y"}<br />
<div class="simplebox">{icon _id=information.png style="vertical-align:middle" align="left"} {tr}If you want people to be able to reply to you, you have to check <a href='tiki-user_preferences.php'>Allow messages from other users</a>{/tr}</div>
{/if}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Separate multiple usernames with a comma (&nbsp;,&nbsp;).{/tr}{/remarksbox}

<form action="messu-compose.php" method="post">
<table class="normal" >
  <tr>
    <td class="formcolor"><label for="mess-composeto">{tr}To{/tr}:</label></td><td class="formcolor"><input type="text" name="to" id="mess-composeto" value="{$to|escape}" />
		<input type="hidden" name="replyto_hash" value="{$replyto_hash}" />
		<input type="hidden" name="reply" value="{$reply}" />
</td>
  </tr>
  <tr>
    <td class="formcolor"><label for="mess-composecc">{tr}CC{/tr}:</label></td><td class="formcolor"><input type="text" name="cc" id="mess-composecc" value="{$cc|escape}" /></td>
  </tr>
  <tr>
    <td class="formcolor"><label for="mess-composebcc">{tr}BCC{/tr}:</label></td><td class="formcolor"><input type="text" name="bcc" id="mess-composebcc" value="{$bcc|escape}" /> </td>
  </tr>
  <tr>
    <td class="formcolor"><label for="mess-prio">{tr}Priority{/tr}:</label></td><td class="formcolor">
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
    <td class="formcolor"><label for="mess-subj">{tr}Subject{/tr}:</label></td><td class="formcolor"><input type="text" name="subject" id="mess-subj" value="{$subject|escape}" size="80" maxlength="255"/></td>
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
<br />
