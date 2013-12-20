{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<table class="table">
	<thead>
		<tr>
			<th>Query</th>
			<th>Last Modification</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		{foreach $queries as $query}
			<tr>
				<td>
					<a href="tiki-searchindex.php?storedQuery={$query.queryId|escape}">{$query.label|escape}</a>
					<span class="label {$priorities[$query.priority].class|escape}">{$priorities[$query.priority].label|escape}</span>
				</td>
				<td>
					{if $query.lastModif}
						{$query.lastModif|tiki_short_datetime}
					{else}
						{tr}Never{/tr}
					{/if}
				</td>
				<td>
					<a class="query-remove" data-confirm="{tr _0=$query.label}Do you really want to remove the %0 query?{/tr}" href="{service controller=search_stored action=delete queryId=$query.queryId}">{icon _id=cross}</a>
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td>
					{tr}No stored queries!{/tr}
				</td>
				<td>{tr}Never{/tr}</td>
				<td>&nbsp;</td>
			</tr>
		{/foreach}
	</tbody>
</table>
{jq}
$('.query-remove').requireConfirm({
	success: function () {
		$(this).closest('tr').remove();
	}
});
{/jq}
{/block}
