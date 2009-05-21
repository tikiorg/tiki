{* $Id$ *}
{if !empty($userGroups)}
{cycle values="odd,even" print=false}
<table class="normal">
{foreach from=$userGroups key=gr item=type}
	<tr>
	<td class="{cycle advance=false}">
		{if $type eq 'included'}<i>{$gr|escape}</i>{else}{$gr|escape}{/if}
		{if $showdefault eq 'y' and $default_group eq $gr}{icon _id='group' alt='{tr}Your default group{/tr}'}{/if}
		{if $showgroupdescription eq 'y'}<div style="margin-left:10px">{$groupDescs.$gr|escape}</div>{/if}
	</td>
	<td class="{cycle}">
		{if $type ne 'included'}
			<a href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}unassign={$gr|escape:'url'}">{icon _id='cross' alt='{tr}Unsubscribe{/tr}'}</a>
		{/if}
		{if $showdefault eq 'y' and  $default_group ne $gr}
			<a href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}default={$gr|escape:'url'}" title="{tr}Change default group{/tr}">{icon _id='group' alt='{tr}Change default group{/tr}'}</a>
		{/if}
	</td>
	</tr>
{/foreach}
</table>
{/if}

{if $showsubscribe ne 'n' && !empty($possiblegroups)}
<form method="post">
<select name="assign" onchange="this.form.submit();">
<option value=""><i>{if !empty($subscribe)}{$subscribe|escape}{else}{tr}Subscribe to a group{/tr}{/if}</i></option>
{foreach from=$possiblegroups item=gr}
	{if $gr.userChoice eq 'y' and empty($userGroups[$gr.groupName])}
	<option value="{$gr.groupName|escape}">
			{$gr.groupName|escape}
			{if $showgroupdescription eq 'y' and !empty($gr.groupDesc)} ({$gr.groupDesc|escape}){/if}
	</option>
	{/if}
{/foreach}
</select>
</form>
{/if}