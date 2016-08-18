{* $Id$ *}
<div class="subscribeGroups">
{if !empty($userGroups)}
<h3>{tr}Groups you are in{/tr}</h3>

<div class="table-responsive">
<table class="table">
{foreach from=$userGroups key=gr item=type}
	<tr>
	<td>
		{if !empty($allGroups.$gr.groupHome)}<a href="{$allGroups.$gr.groupHome|escape:url}" class="groupLink">{/if}
		{if $type eq 'included'}{$gr|escape} <i>{tr}(This is an included group){/tr}</i>
		{elseif $type eq 'leader'}{$gr|escape} <i>{tr}(You are a leader){/tr}</i>
		{else}{$gr|escape}{/if}
		{if !empty($allGroups.$gr.groupHome)}</a>{/if}
		{if $showdefault eq 'y' and $default_group eq $gr}{icon name='group' class='tips' title=":{tr}Your default group{/tr}"}{/if}
		{if $showgroupdescription eq 'y'}<div>{$allGroups.$gr.groupDesc|escape}</div>{/if}
	</td>
	<td>
		{if $type ne 'included' and $type ne 'leader' and ($alwaysallowleave eq 'y' or $allGroups.$gr.userChoice eq 'y')}
			<a href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}unassign={$gr|escape:'url'}" class="button">{tr}Leave Group{/tr}</a>
		{/if}
		{if $type eq 'leader'}
			<a class="button" href="{$managementpages.$gr|sefurl:wiki}">{tr}Manage Group{/tr}</a>
		{/if}
		{if $showdefault eq 'y' and ($default_group ne $gr or !empty($defaulturl))}
			<a href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}default={$gr|escape:'url'}" class="tips" title=":{tr}Change default group{/tr}">{icon name='group'}</a>
		{/if}
	</td>
	</tr>
{/foreach}
</table>
</div>
{/if}

{if $showsubscribe ne 'n' && !empty($possibleGroups) && $subscribestyle eq 'dropdown'}
<form method="post">
<select name="assign" onchange="this.form.submit();">
<option value=""><i>{if !empty($subscribe)}{$subscribe|escape}{else}{tr}Subscribe to a group{/tr}{/if}</i></option>
{foreach from=$possibleGroups item=gr}
	<option value="{$gr|escape}">
		{$gr|escape}
		{if $showgroupdescription eq 'y' and !empty($allGroups.$gr.groupDesc)} ({$allGroups.$gr.groupDesc|escape}){/if}
	</option>
{/foreach}
</select>
</form>
{elseif $showsubscribe ne 'n' && !empty($possibleGroups) && $subscribestyle eq 'table'}
<h3{if !empty($userGroups)} style="margin-top: 15px;"{/if}>{tr}Groups you can join{/tr}</h3>
<form method="post">
<div class="table-responsive">
<table class="table">
{foreach from=$possibleGroups item=gr}
	<tr>
	<td>
	<input name="assign[]" type="checkbox" value="{$gr|escape}">
	{if !in_array($gr, $privategroups)}<a href="{$allGroups.$gr.groupHome|escape:url}" class="groupLink">{else}<span class="groupLink">{/if}{if isset($basegroupnames.$gr)}{$basegroupnames.$gr|escape} <i>{tr}This group requires approval to join{/tr}</i>{else}{$gr|escape}{/if}{if !in_array($gr, $privategroups)}</a>{else}</span>{/if}
	{if $showgroupdescription eq 'y'}<div style="padding-left: 25px;">{$allGroups.$gr.groupDesc|escape}</div>{/if}
	</td>
	</tr>
{/foreach}
</table>
</div>
<input type="submit" class="btn btn-default btn-sm" value="{if !empty($subscribe)}{$subscribe|escape}{else}{tr}Subscribe to groups{/tr}{/if}">
</form>{/if}
</div>
