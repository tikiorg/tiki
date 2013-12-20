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
		</tr>
	</thead>
	<tbody>
		{foreach $queries as $query}
			<tr>
				<td>
					{$query.label|escape}
					<span class="label {$priorities[$query.priority].class|escape}">{$priorities[$query.priority].label|escape}</span>
				</td>
				<td>
					{if $query.lastModif}
						{$query.lastModif|tiki_short_datetime}
					{else}
						{tr}Never{/tr}
					{/if}
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td>
					{tr}No stored queries!{/tr}
				</td>
				<td>{tr}Never{/tr}</td>
			</tr>
		{/foreach}
	</tbody>
</table>
{/block}
