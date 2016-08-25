{extends 'layout_view.tpl'}

{block name="navigation"}
	{include file='tracker_actions.tpl'}
	<a class="btn btn-default" href="{service controller=tracker action=select_tracker}">{tr}Select Tracker{/tr}</a>
{/block}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if ! $itemId}
		{if $trackerLogo}
			<div class="page_header media">
				<img src="{$trackerLogo|escape}" class="pull-left img-responsive img-rounded" alt="{$trackerName|escape}" height="64px" width="64px">
			</div>
		{/if}
		<form method="post" action="{service controller=tracker action=insert_item format=$format editItemPretty=$editItemPretty}" id="insertItemForm{$trackerId|escape}" {if ! $trackerId}display="hidden"{/if}>
			{trackerfields trackerId=$trackerId fields=$fields status=$status format=$format  editItemPretty=$editItemPretty}
			{if ! $modal}
				<div class="checkbox">
					<label>
						<input type="checkbox" name="next" value="{service controller=tracker action=insert_item trackerId=$trackerId}">
						{tr}Create another{/tr}
					</label>
				</div>
			{/if}
			{if !$user and $prefs.feature_antibot eq 'y'}
				{include file='antibot.tpl'}
			{/if}
			<div class="submit">
				<input type="hidden" name="trackerId" value="{$trackerId|escape}">
				<input type="submit" class="btn btn-primary" value="{tr}Create{/tr}" onclick="needToConfirm=false;">
				{foreach from=$forced key=permName item=value}
					<input type="hidden" name="forced~{$permName|escape}" value="{$value|escape}">
				{/foreach}
			</div>
		</form>
	{else}
		{object_link type=trackeritem id=$itemId}
	{/if}
{/block}
