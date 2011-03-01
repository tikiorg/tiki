{* $Id$ *}
{capture}{if isset($smarty.request.objectName)}{tr}Object Watches:{/tr} {$smarty.request.objectName|escape}{else}{tr}Object Watches{/tr}{/if}{/capture}
{title help="Mail notifications"}{$smarty.capture.default}{/title}

{if isset($referer)}
	<div class="navbar">
		{button href="$referer" _text="{tr}Back{/tr}"}
	</div>
{/if}

{if !empty($addedGroups) || !empty($deletedGroups) || !empty($addedGroupsDesc) || !empty($deletedGroupsDesc)}
	{remarksbox type="feedback"}
	{if !empty($addedGroups) || !empty($deletedGroups)}
		<div style="float:left;clear:both;">
			{tr}Changes to groups watching:{/tr}
			<ul>
			{if !empty($addedGroups)}
				{foreach from=$addedGroups item=g}<li>{$g|escape}&nbsp;&nbsp;<em>added</em></li>{/foreach}
			{/if}
			{if !empty($deletedGroups)}
				{foreach from=$deletedGroups item=g}<li>{$g|escape}&nbsp;&nbsp;<em>removed</em></li>{/foreach}
			{/if}
			</ul>
		</div>
	{/if}
	{if !empty($addedGroupsDesc) || !empty($deletedGroupsDesc)}
		{if !empty($addedGroups) || !empty($deletedGroups)}
			<div style="float:left;padding-left:50px;">
		{else}
			<div style="float:left;">
		{/if}
			{tr}These changes to group watches:{/tr}
			<ul>
			{if !empty($addedGroupsDesc)}
				{foreach from=$addedGroupsDesc item=g}<li>{$g|escape}&nbsp;&nbsp;<em>added</em></li>{/foreach}
			{/if}
			{if !empty($deletedGroupsDesc)}
				{foreach from=$deletedGroupsDesc item=g}<li>{$g|escape}&nbsp;&nbsp;<em>removed</em></li>{/foreach}
			{/if}
			</ul>
			{if !empty($catTreeNodes)} 
				{tr}were made to these descendants:{/tr}
				{$tree}
			{/if}
		</div>
	{/if}
	{/remarksbox}
{/if}

<form method="post" action="{$smarty.server.REQUEST_URI|escape}">
<input type="hidden" name="referer" value="{$referer|escape}" />
<div style="float: left; margin-right: 10px;"><input type="submit" name="assign" value="{tr}Apply{/tr}" /></div>
{if $isTop ne 'y'}
	<p>{tr}Groups watching:{/tr} {$group_watches|@count}</p>
{else}
	<p>&nbsp;</p>
{/if}
<table class="normal">
	<tr>
		{if !empty($cat) && !empty($desc)}
			<th>{tr}Groups{/tr}</th>
			{if $isTop ne 'y'}
				<th>{tr}This Category{/tr}</th>
			{/if}
			<th>{tr}All Descendants{/tr}</th>
		{else}
		<th>
			{select_all checkbox_names='checked[]'}
		</th>
		<th style="width:100%">{tr}Groups{/tr}</th>
		{/if}
	</tr>
	{cycle values="odd,even" print=false}
	{foreach from=$all_groups item=g key=i}
		{if $g ne 'Anonymous'}
			<tr class="{cycle}">
				{if !empty($cat) && !empty($desc)}
					<td class="text"><label for="group_watch{$i}">{$g|escape}</label></td>
					{if $isTop ne 'y'}
						<td class="checkbox"><input id="group_watch{$i}"type="checkbox" name="checked[]" 
						value="{$g|escape}"{if in_array($g, $group_watches)} checked="checked"{/if} /></td>
					{/if}
					<td class="text">
						<input id="group_watch{$i}" type="radio" name="{$g|escape}" value="cat_leave_desc" checked="checked" /> 
						<label for="group_watch{$i}">Leave unchanged &nbsp;&nbsp;&nbsp;</label>
						<input id="group_watch{$i}" type="radio" name="{$g|escape}" value="cat_add_desc" /> 
						<label for="group_watch{$i}">Add &nbsp;&nbsp;&nbsp;</label>
						<input id="group_watch{$i}" type="radio" name="{$g|escape}" value="cat_remove_desc" /> 
						<label for="group_watch{$i}">Remove</label>
					</td>
					
				{else}
				<td class="checkbox"><input id="group_watch{$i}" type="checkbox" name="checked[]" value="{$g|escape}"
					{if in_array($g, $group_watches)} checked="checked"{/if} /></td>
				<td class="text"><label for="group_watch{$i}">{$g|escape}</label></td>
				{/if}
			</tr>
		{/if}
	{/foreach}
</table>
	<p><div style="float: left; margin-right: 10px;"><input type="submit" name="assign" value="{tr}Apply{/tr}" /></div></p>
</form>
