{extends 'layout_view.tpl'}

{block name="navigation"}
	{include file='tracker_actions.tpl'}
{/block}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<form class="form" method="post" action="{service controller=tracker action=select_tracker}" id="selectTrackerForm" role="form">
		<div class="form-group">
			<label class="control-label" for="trackerId">
				{tr}Tracker{/tr}
			</label>
			<select name="trackerId" id="trackerId" class="form-control" required="required">
				{foreach from=$trackers item=tr key=k}
					<option value="{$tr.trackerId|escape}">{$tr.name|escape}</option>
				{/foreach}
			</select>
		</div>
		<div class="submit text-center">
			<input type="hidden" name="confirm" value="1">
			<input type="submit" class="btn btn-primary" data-dismiss="modal" value="{tr}Select{/tr}">
		</div>
	</form>
{/block}
