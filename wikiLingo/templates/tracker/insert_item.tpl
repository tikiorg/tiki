{extends 'layout_view.tpl'}

{block name="navigation"}
	{include file='tracker_actions.tpl'}
{/block}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if ! $itemId}
		<div class="page-header media">
			{if $trackerLogo}
				<img src="{$trackerLogo}" class="pull-left img-responsive img-rounded" alt="Responsive image" height="64px" width="64px">
			{/if}
			<div class="media-body">
				<label class="control-label media-heading" for="trackerName">
					{tr}Tracker{/tr}
				</label>
				<div class="input-group">
					<input type="text" name="trackerName" class="form-control" value="{$trackerName|escape}" {if $trackerId}disabled{/if}>
					<div class="input-group-btn">
						<a class="btn btn-default" href="{service controller=tracker action=select_tracker}">
							{tr}Select Tracker{/tr}
						</a>
					</div>
				</div>
			</div>
		</div>
		<form class="simple" method="post" action="{service controller=tracker action=insert_item}" id="insertItemForm" {if ! $trackerId}display="hidden"{/if}>
			{trackerfields trackerId=$trackerId fields=$fields}
			<div class="submit text-center">
				<input type="hidden" name="trackerId" value="{$trackerId|escape}">
				<div class="checkbox">
					<label>
					  <input type="checkbox">{tr}Create another{/tr}
					</label>
				  </div>
				<input type="submit" class="btn btn-primary" value="{tr}Create{/tr}">
				{foreach from=$forced key=permName item=value}
					<input type="hidden" name="forced~{$permName|escape}" value="{$value|escape}">
				{/foreach}
			</div>
		</form>
	{else}
		{object_link type=trackeritem id=$itemId}
	{/if}
{/block}