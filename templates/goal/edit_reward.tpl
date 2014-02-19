{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	{remarksbox title="{tr}Changes will not be saved{/tr}"}
		{tr}Your changes to rewards are not saved until you save the goal.{/tr}
	{/remarksbox}
	<form class="form-horizontal reward-form" method="post" action="{service controller=goal action=edit_reward}">
		<div class="form-group">
			<label class="control-label col-md-3">{tr}Type{/tr}</label>
			<div class="col-md-9">
				<select name="rewardType" class="form-control">
					{foreach $rewards as $key => $info}
						<option value="{$key|escape}" {if $reward.rewardType eq $key} selected {/if} data-arguments="{$info.arguments|json_encode|escape}">{$info.label|escape}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="form-group argument eventType">
			<label class="control-label col-md-3">{tr}Credit Type{/tr}</label>
			<div class="col-md-9">
				<input type="text" class="form-control" name="creditType" value="{$reward.creditType|escape}">
			</div>
		</div>
		<div class="form-group argument eventType">
			<label class="control-label col-md-3">{tr}Credit Quantity{/tr}</label>
			<div class="col-md-9">
				<input type="text" class="form-control" name="creditQuantity" value="{$reward.creditQuantity|escape}">
			</div>
		</div>
		<div class="checkbox col-md-offset-3">
			<label>
				<input type="checkbox" name="hidden" value="1" {if $reward.hidden}checked{/if}>
				{tr}Hide reward from users{/tr}
			</label>
		</div>
		<div class="submit col-md-offset-3">
			<input type="submit" class="btn btn-primary" value="{tr}Apply{/tr}">
		</div>
	</form>
	{jq}
		$('.reward-form select[name=metric]').change(function () {
			$('.reward-form .form-group.argument').hide();

			$.each(this.selectedOptions, function (key, item) {
				$.each($(item).data('arguments'), function (key, arg) {
					$('.reward-form .form-group.argument.' + arg).show();
				});
			})
		}).change();
	{/jq}
{/block}
