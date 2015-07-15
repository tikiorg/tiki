{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="navigation"}
<div class="navbar">
	<a class="btn btn-default" href="{bootstrap_modal controller=tabular action=filter tabularId=$tabularId target=list _params=$baseArguments}">{icon name=filter}{tr}Filter{/tr}</a>
	{permission name=tabular_edit type=tabular object=$tabularId}
		<a class="btn btn-default" href="{service controller=tabular action=edit tabularId=$tabularId}">{icon name=edit}{tr}Edit{/tr}</a>
	{/permission}
	{permission name=tabular_export type=tabular object=$tabularId}
		<a class="btn btn-default" href="{bootstrap_modal controller=tabular action=filter tabularId=$tabularId target=export _params=$baseArguments}">{icon name=export}{tr}Export{/tr}</a>
	{/permission}
	{permission name=admin_trackers}
		<a class="btn btn-default" href="{service controller=tabular action=manage}">{icon name=list} {tr}Manage{/tr}</a>
	{/permission}
</div>
{/block}

{block name="content"}
{if $filters.primary.usable}
	<form class="form-horizontal" method="get" action="{service controller=tabular action=list}">
		{foreach $filters.primary.controls as $filter}
			<div class="form-group">
				<label class="col-sm-3 control-label" for="{$filter.id|escape}">{$filter.label|escape}</label>
				<div class="col-sm-9">
					{$filter.control}
				</div>
			</div>
		{/foreach}
		<div class="submit form-group">
			<div class="hidden">
				<input type="hidden" name="tabularId" value="{$tabularId|escape}">
				{* Include default filters to preserve them *}
				{* Exclude side filters to reset them, as they are secondary *}
				{foreach $filters.default.controls as $filter}
					{$filter.control}
				{/foreach}
			</div>
			<div class="col-sm-9 col-sm-push-3">
				<input class="btn btn-primary" type="submit" value="{tr}Search{/tr}">
			</div>
		</div>
	</form>
{/if}
{if $filters.default.selected}
	<h4>{tr}Applied filters{/tr}</h4>
	<dl class="dl-horizontal">
		{foreach $filters.default.controls as $filter}
			{if $filter.selected}
				<dt>{$filter.label|escape}</dt>
				<dd>{$filter.description|escape}</dd>
			{/if}
		{/foreach}
	</dl>
{/if}
{if $filters.side.usable}
	<div class="row">
		<div class="col-md-9">
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
		</div>
		<div class="col-md-3">
			<form method="get" action="{service controller=tabular action=list}">
				{foreach $filters.side.controls as $filter}
					<div class="form-group">
						<label class="control-label" for="{$filter.id|escape}">{$filter.label|escape}</label>
						{$filter.control}
					</div>
				{/foreach}
				<div class="form-group submit">
					<div class="hidden">
						<input type="hidden" name="tabularId" value="{$tabularId|escape}">

						{* Include default filters to preserve them *}
						{* Include primary filters to preserve them, as they are higher *}
						{foreach $filters.default.controls as $filter}
							{$filter.control}
						{/foreach}
						{foreach $filters.primary.controls as $filter}
							{$filter.control}
						{/foreach}
					</div>
					<input class="btn btn-default" type="submit" value="{tr}Filter{/tr}">
				</div>
			</form>
		</div>
	</div>
{else}
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
{/if}
{/block}
