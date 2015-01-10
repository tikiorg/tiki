{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form class="no-ajax" method="post" action="{service controller="tabular" action="export_partial_csv"}">
	{foreach $filters as $filter}
		<div class="form-group">
			<label class="control-label" for="{$filter.id|escape}">{$filter.label|escape}</label>
			{$filter.control}
		</div>
	{/foreach}
	<div class="submit">
		<input type="hidden" name="tabularId" value="{$tabularId|escape}"/>
		<input type="submit" class="btn btn-primary" value="{tr}Export{/tr}">
	</div>
</form>
{/block}
