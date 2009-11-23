{* $Id$ *}
{title help="Mail notifications"}{tr}Object Watches:{/tr} {$smarty.request.objectName|escape}{/title}

{if isset($referer)}
	<div class="navbar">
		{button href="$referer" _text="{tr}Back{/tr}"}
	</div>
{/if}

{if !empty($addedGroups) || !empty($deletedGroups)}
{remarksbox type="feedback"}
	{if !empty($addedGroups)}
		{tr}Added:{/tr}<ul>
		{foreach from=$addedGroups item=g}<li>{$g|escape}</li>{/foreach}
		</ul>
	{/if}
	{if !empty($deletedGroups)}
		{tr}Removed:{/tr}<ul>
		{foreach from=$deletedGroups item=g}<li>{$g|escape}</li>{/foreach}
	{/if}
{/remarksbox}
{/if}

<form method="post" action="{$smarty.server.REQUEST_URI}">
<input type="hidden" name="referer" value="{$referer|escape}" />
<div><input type="submit" name="assign" value="{tr}Assign{/tr}" /></div>
<p>{tr}Watching Groups:{/tr} {$group_watches|@count}</p>
<table class="normal">
	<tr>
		<th>
			{select_all checkbox_names='checked[]'}
		</th>
		<th style="width:100%">{tr}Groups{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{foreach from=$all_groups item=g key=i}
		{if $g ne 'Anonymous'}
			<tr class="{cycle}">
				<td><input id="group_watch{$i}"type="checkbox" name="checked[]" value="{$g|escape}"{if in_array($g, $group_watches)} checked="checked"{/if} /></td>
				<td><label for="group_watch{$i}">{$g|escape}</label></td>
			</tr>
		{/if}
	{/foreach}
</table>
<div><input type="submit" name="assign" value="{tr}Assign{/tr}" /></div>
</form>
