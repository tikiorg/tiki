{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form method="post" action="{service controller=workspace action=select_permissions}">
	<table class="data">
		<tr>
			<th>{tr}Permissions{/tr}</th>
			{foreach from=$groups item=group}
				<th>{$group|escape}</th>
			{/foreach}
		</tr>

		{foreach from=$descriptions key=type item=block}
			<tr>
				<th colspan="{$permissions|count + 1}">{$type|escape}</th>
			</tr>
			{foreach from=$block item=row}
				<tr>
					<td>
						{$row.description|escape}
						<div class="description help-block">
							{$row.name|escape}
						</div>
					</td>
					{foreach from=$permissions key=group item=perms}
						<td>
							<input type="checkbox" name="check[{$group|escape}][]" value="{$row.shortName|escape}" {if in_array($row.shortName, $perms)} checked="checked" {/if} />
						</td>
					{/foreach}
				</tr>
			{/foreach}
		{/foreach}
	</table>
</form>
{/block}
