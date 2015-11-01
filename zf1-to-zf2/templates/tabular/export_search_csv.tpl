{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form class="no-ajax" method="get" action="{service controller="tabular" action="export_search_csv"}">
	<label class="control-label">{tr}Format{/tr}</label>
	<select name="tabularId" class="form-control">
		{foreach $formats as $format}
			<option value="{$format.tabularId|escape}">{$format.name|escape}</option>
		{/foreach}
	</select>
	<div class="submit">
		{foreach $filters as $key => $value}
			<input type="hidden" name="filter~{$key|escape}" value="{$value|escape}">
		{/foreach}
		<input type="submit" class="btn btn-primary" value="{tr}Export{/tr}">
	</div>
</form>
{/block}
