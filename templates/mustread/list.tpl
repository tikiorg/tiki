{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
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
				<tr>
					<td><a class="detail-link" href="{service controller=mustread action=detail id=$entry.object_id plain=1}" data-target=".mustread-container">{$entry.title|escape}</a></td>
					<td>{$entry.creation_date|tiki_short_datetime}</td>
					<td>
						{if $entry.reason eq 'owner'}
							{tr}None (Owner){/tr}
						{/if}
					</td>
					<td><input type="checkbox" name="complete[]" value="{$entry.object_id|escape}"></td>
				</tr>
			{/foreach}
		</table>
	</form>
	<div class="mustread-container">
	</div>
{/block}
