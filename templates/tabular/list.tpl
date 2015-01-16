{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form method="get" action="{service controller="tabular" action="list"}">
	{foreach $filters as $filter}
		<div class="form-group">
			<label class="control-label" for="{$filter.id|escape}">{$filter.label|escape}</label>
			{$filter.control}
		</div>
	{/foreach}
	<div class="submit">
		<input type="hidden" name="tabularId" value="{$tabularId|escape}"/>
		<input type="submit" class="btn btn-primary" value="{tr}Filter{/tr}">
	</div>
</form>
<table class="table">
	<tr>
		{foreach $columns as $column}
			{if ! $column->isExportOnly()}
				<th>{$column->getLabel()}</th>
			{/if}
		{/foreach}
	</tr>
	{foreach $data as $row}
		<tr>
			{foreach $row as $col}
				<td>{$col}</td>
			{/foreach}
		</td>
	{/foreach}
</table>
{pagination_links resultset=$resultset}{/pagination_links}
{/block}
