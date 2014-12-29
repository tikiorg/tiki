{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="navigation"}
	{include file='tracker_actions.tpl'}
{/block}

{block name="content"}
<form method="post" action="{service controller=tracker action=update_item format=$format}" id="updateItemForm{$trackerId|escape}">
	{trackerfields trackerId=$trackerId fields=$fields status=$status itemId=$itemId format=$format}
	<div class="submit">
		<input type="hidden" name="itemId" value="{$itemId|escape}">
		<input type="hidden" name="trackerId" value="{$trackerId|escape}">
		<input type="submit" class="btn btn-primary" value="{tr}Save{/tr}">
		{foreach from=$forced key=permName item=value}
			<input type="hidden" name="forced~{$permName|escape}" value="{$value|escape}">
		{/foreach}
	</div>
</form>
{/block}
