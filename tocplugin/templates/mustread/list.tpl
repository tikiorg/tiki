{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="quicknav"}
	{monitor_link type=user object=$user}
{/block}

{block name="content"}
	<form method="post" action="{service controller=mustread action=mark}">
		<table class="table">
			<tr>
				<th>{tr}Entry{/tr}</th>
				<th>{tr}Request Date{/tr}</th>
				<th>{tr}Action Req'd{/tr}</th>
				<th>{tr}Complete{/tr}</th>
			</tr>
			{foreach $list as $entry}
				<tr {if $selection eq $entry.object_id}class="active"{/if}>
					<td><a href="{service controller=mustread action=list id=$entry.object_id}">{$entry.title|escape}</a></td>
					<td>{$entry.creation_date|tiki_short_datetime}</td>
					<td>
						{if $entry.reason eq 'owner'}
							{tr}None (Owner){/tr}
						{elseif $entry.reason eq 'circulation'}
							{tr}Circulate{/tr}
						{elseif $entry.reason eq 'comment'}
							{tr}Comment{/tr}
						{elseif $entry.reason eq 'required'}
							{tr}Read{/tr}
						{/if}
					</td>
					<td><input type="checkbox" name="complete[]" value="{$entry.object_id|escape}"></td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="4">{tr}Nothing to do.{/tr}</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="3">
					{if $canAdd}
						<a class="btn btn-default add-mustread-item" href="{service controller=tracker action=insert_item trackerId=$prefs.mustread_tracker modal=1}">{icon name="add"} {tr}Add Item{/tr}</a>
					{/if}
					&nbsp;
				</td>
				<td>
					{if $list|count > 0}
						<input class="btn btn-primary" type="submit" value="{tr}Save{/tr}">
					{/if}
				</td>
			</tr>
		</table>
	</form>
	<div class="mustread-container">
		{if $selection}
			{service_inline controller=mustread action=detail id=$selection notification=$notification}
		{/if}
	</div>
{/block}
