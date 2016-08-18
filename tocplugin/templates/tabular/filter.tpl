{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form class="no-ajax" method="{$method|escape}" action="{service controller="tabular" action=$action}">
	{foreach $filters as $filter}
		<div class="form-group">
			<label class="control-label" for="{$filter.id|escape}">{$filter.label|escape}</label>
			{$filter.control}
		</div>
	{/foreach}
	<div class="submit">
		<input type="hidden" name="tabularId" value="{$tabularId|escape}"/>
		<input type="hidden" name="controller" value="tabular"/>
		<input type="hidden" name="action" value="{$action|escape}"/>
		<input type="submit" class="btn btn-primary" value="{$label|escape}">
	</div>
</form>
{/block}
