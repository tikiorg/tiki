<a class="pagetitle" href="messu-broadcast.php">{tr}Broadcast message{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
{include file="messu-nav.tpl"}
<br/><br/>
{if $sent}
{$message}
{else}
<form action="messu-broadcast.php" method="post">
<table class="normal" width="70%">
  <tr>
    <td class="formcolor">{tr}Group{/tr}:</td>
    <td class="formcolor">
    <select name="group">
    {if $tiki_p_broadcast_all eq 'y'}
    <option value="all" selected="selected">{tr}All users{/tr}</option>
    {/if}
	{section name=ix loop=$groups}
	<option value="{$groups[ix].groupName}">{$groups[ix].groupName}</option>
	{/section}
    </select>
    </td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Priority{/tr}:</td><td class="formcolor">
    <select name="priority">
      <option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
      <option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
      <option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
      <option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
      <option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
    </select>
    <input type="submit" name="send" value="{tr}send{/tr}" />
    </td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Subject{/tr}:</td><td class="formcolor"><input type="text" name="subject" value="{$subject}" size="80" maxlength="255"/></td>
  </tr>
</table>
<br/>
<table class="normal" width="70%">
  <tr>
    <td style="text-align: center;" class="formcolor"><textarea rows="20" cols="80" name="body">{$body}</textarea></td>
  </tr>
</table>
</form>
{/if}
<br/><br/>