{* $Id$ *}
{if !empty($userGroups)}
{cycle values="odd,even" print=false}
<table class="normal">
{foreach from=$userGroups key=group item=type}
	<tr>
	<td class="{cycle advance=false}">{if $type eq 'included'}<i>{$group|escape}</i>{else}{$group|escape}{/if}</td>
	<td class="{cycle}">{if $type ne 'included'}<a href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}unassign={$group|escape:'url'}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt="{tr}Unsubscribe{/tr}" /></a>{/if}</td>
	</tr>
{/foreach}
</table>
{/if}

{if !empty($possiblegroups)}
<form method="post">
<select name="assign" onchange="this.form.submit();">
<option value=""><i>{if isset($subscribe)}{subscribe|escape|{else}{tr}Subscribe to a group{/tr}{/if}</i></option>
{foreach from=$possiblegroups item=group}
	{if $group.userChoice eq 'y' and empty($userGroups[$group.groupName])}
	<option value="{$group.groupName|escape}">{$group.groupName}</option>
	{/if}
{/foreach}
</select>
</form>
{/if}