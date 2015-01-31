{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="navigation"}
<div class="navbar">
	<a class="btn btn-default" href="{bootstrap_modal controller=tabular action=filter tabularId=$tabularId target=list _params=$baseArguments}">{icon name=filter}{tr}Filter{/tr}</a>
	{permission name=tabular_export type=tabular object=$tabularId}
		<a class="btn btn-default" href="{bootstrap_modal controller=tabular action=filter tabularId=$tabularId target=export _params=$baseArguments}">{icon name=export}{tr}Export{/tr}</a>
	{/permission}
	{permission name=admin_trackers}
		<a class="btn btn-default" href="{service controller=tabular action=manage}">{icon name=list} {tr}Manage{/tr}</a>
	{/permission}
</div>
{/block}

{block name="content"}
<dl class="dl-horizontal">
	{foreach $filters as $filter}
		{if $filter.description}
			<dt>{$filter.label|escape}</dt>
			<dd>{$filter.description|escape}</dd>
		{/if}
	{/foreach}
</dl>
<table class="table">
	<tr>
		{foreach $columns as $column}
			<th class="text-{$column->getDisplayAlign()|escape}">{$column->getLabel()}</th>
		{/foreach}
	</tr>
	{foreach $data as $row}
		<tr>
			{foreach $row as $i => $col}
				<td class="text-{$columns[$i]->getDisplayAlign()|escape}">{$col}</td>
			{/foreach}
		</td>
	{/foreach}
</table>
{pagination_links resultset=$resultset}{service controller=tabular action=list tabularId=$tabularId _params=$baseArguments}{/pagination_links}
{/block}
