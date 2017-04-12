{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="navigation"}
	{if $skip_form neq 'y'}
		{include file='tracker_actions.tpl'}
	{/if}
{/block}

{block name="content"}
	{if $skip_form eq 'y'}
		<form method="post" class="confirm-action" action="{service controller=tracker
		action=update_item
		trackerId={$trackerId}
		itemId={$itemId}
		}">
			<p>
				{$skip_form_message}
			</p>
			<div class="submit">
				<input type="hidden" name="status" value="{$status|escape}">
				{foreach from=$forced key=permName item=value}
					<input type="hidden" name="forced~{$permName|escape}" value="{$value|escape}">
				{/foreach}
				<input type="hidden" name="redirect" value="{$redirect|escape}">
				<input type="submit" class="btn btn-primary" value="{$button_label}" onclick="needToConfirm=false;">
			</div>
		</form>
	{else}
		<form method="post" class="confirm-action" action="{service controller=tracker action=update_item format=$format}" id="updateItemForm{$trackerId|escape}">
			{trackerfields trackerId=$trackerId fields=$fields status=$status itemId=$itemId format=$format}
			<div class="submit">
				<input type="hidden" name="itemId" value="{$itemId|escape}">
				<input type="hidden" name="trackerId" value="{$trackerId|escape}">
				{foreach from=$forced key=permName item=value}
					<input type="hidden" name="forced~{$permName|escape}" value="{$value|escape}">
				{/foreach}
				<input type="hidden" name="redirect" value="{$redirect|escape}">
				<input type="submit" class="btn btn-primary" value="{$button_label}" onclick="needToConfirm=false;">
			</div>
		</form>
		{* Don't warn on leaving page if the modal is closed without saving *}
		{jq}$(".modal.fade.in").one("hide.bs.modal", function () {window.needToConfirm=false;});{/jq}
	{/if}
{/block}
