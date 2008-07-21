<h1><a class="pagetitle" href="messu-broadcast.php">{tr}Broadcast message{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Inter-User Messages" target="tikihelp" class="tikihelp" title="{tr}Message Broadcast{/tr}">
{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=messu-broadcast.tpl" target="tikihelp" class="tikihelp">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>

{include file=tiki-mytiki_bar.tpl}
{include file="messu-nav.tpl"}
<br /><br />


{if $message}
<div class="simplebox highlight">{if $sent ne '1'}{icon _id=exclamation style="vertical-align:middle" alt="{tr}Error{/tr}"}{else}{icon _id=accept alt="{tr}OK{/tr}" style="vertical-align:middle;"}{/if} {$message}</div><br />
<br /><br />
{/if}
{if $sent ne '1'}
<form action="messu-broadcast.php" method="post">
<table class="normal" >
  <tr>
    <td class="formcolor"><label for="broadcast-group">{tr}Group{/tr}:</label></td>
    <td class="formcolor">
    <select name="group" id="broadcast-group">
    {if $tiki_p_broadcast_all eq 'y'}
    <option value="all" selected="selected">{tr}All users{/tr}</option>
    {/if}
	{section name=ix loop=$groups}
	{if $groups[ix] ne "Anonymous"}<option value="{$groups[ix]|escape}">{$groups[ix]}</option>{/if}
	{/section}
    </select>
    </td>
  </tr>
  <tr>
    <td class="formcolor"><label for="broadcast-priority">{tr}Priority{/tr}:</label></td><td class="formcolor">
    <select name="priority" id="broadcast-priority">
      <option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
      <option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
      <option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
      <option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
      <option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
    </select>
		<input type="hidden" name="replyto_hash" value="{$replyto_hash}" />
    </td>
  </tr>
  <tr>
    <td class="formcolor"><label for="broadcast-subject">{tr}Subject{/tr}:</label></td><td class="formcolor"><input type="text" name="subject" id="broadcast-subject" value="{$subject|escape}" size="80" maxlength="255"/></td>
  </tr>
</table>
<br />
<table class="normal" >
  <tr>
    <td style="text-align: center;" class="formcolor"><textarea rows="20" cols="80" name="body">{$body|escape}</textarea><br /><input type="submit" name="send" value="{tr}Send{/tr}" /></td>
  </tr>
</table>
</form>
{/if}
<br />
