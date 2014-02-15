{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<form class="form-horizontal" method="post" action="{service controller=goal action=edit goalId=$goal.goalId}">
		{tabset}
			{tab name="{tr}General{/tr}"}
				<div class="form-group">
					<div class="checkbox col-md-offset-3">
						<label>
							<input type="checkbox" name="enabled" value="1" {if $goal.enabled}checked{/if}>
							{tr}Enabled{/tr}
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="name" class="control-label col-md-3">{tr}Name{/tr}</label>
					<div class="col-md-9">
						<input type="text" name="name" class="form-control" value="{$goal.name|escape}">
					</div>
				</div>
				<div class="form-group">
					<label for="description" class="control-label col-md-3">{tr}Description{/tr}</label>
					<div class="col-md-9">
						<textarea name="description" class="form-control">{$goal.description|escape}</textarea>
					</div>
				</div>
			{/tab}
			{tab name="{tr}Eligibility{/tr}"}
				<div class="form-group">
					<label class="control-label col-md-3">{tr}Goal Attribution{/tr}</label>
					<div class="col-md-9">
						<label class="radio-inline">
							<input type="radio" name="type" value="user" {if $goal.type neq 'group'}checked{/if}>
							{tr}Individual Goal{/tr}
						</label>
						<label class="radio-inline">
							<input type="radio" name="type" value="group" {if $goal.type eq 'group'}checked{/if}>
							{tr}Group Goal{/tr}
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="eligible" class="control-label col-md-3">{tr}Groups{/tr}</label>
					<div class="col-md-9">
						<select name="eligible[]" class="form-control" multiple>
							{foreach $groups as $groupName}
								<option value="{$groupName|escape}" {if in_array($groupName, $goal.eligible)} selected {/if}>{$groupName|escape}</option>
							{/foreach}
						</select>
					</div>
				</div>
			{/tab}
			{tab name="{tr}Conditions{/tr}"}
				<div class="form-group">
					<label class="control-label col-md-3">{tr}Range Type{/tr}</label>
					<div class="col-md-9">
						<label class="radio-inline">
							<input name="range_type" type="radio" value="rolling" {if $goal.daySpan} checked {/if} data-target="#date-span">
							{tr}Rolling{/tr}
						</label>
						<label class="radio-inline">
							<input name="range_type" type="radio" value="fixed" {if ! $goal.daySpan} checked {/if} data-target="#date-from,#date-to">
							{tr}Fixed{/tr}
						</label>
					</div>
				</div>
				<div class="form-group" id="date-span">
					<label class="control-label col-md-3" for="daySpan">{tr}Time span{/tr}</label>
					<div class="col-md-9">
						<input class="form-control" name="daySpan" type="number" value="{$goal.daySpan|escape}">
						<div class="help-block">
							{tr}In days{/tr}
						</div>
					</div>
				</div>
				<div class="form-group" id="date-from">
					<label class="control-label col-md-3" for="from">{tr}From{/tr}</label>
					<div class="col-md-9">
						<input class="form-control" name="from" type="datetime" value="{$goal.from|escape}" placeholder="{tr}YYYY-MM-DD HH:MM:SS{/tr}">
					</div>
				</div>
				<div class="form-group" id="date-to">
					<label class="control-label col-md-3" for="to">{tr}To{/tr}</label>
					<div class="col-md-9">
						<input class="form-control" name="to" type="datetime" value="{$goal.to|escape}" placeholder="{tr}YYYY-MM-DD HH:MM:SS{/tr}">
					</div>
				</div>
				{jq}
					$(':radio[name=range_type]').change(function () {
						if ($(this).is(':checked')) {
							$(':radio[name=range_type]').each(function () {
								$($(this).data('target')).hide();
							});
							$($(this).data('target')).show();
						}
					}).change();
				{/jq}
			{/tab}
		{/tabset}
		<div class="form-group">
			<div class="col-md-offset-3 col-md-9">
				<input type="submit" class="btn btn-primary" value="{tr}Save{/tr}">
				<a href="{service controller=goal action=admin}" class="btn btn-link">{tr}Cancel{/tr}</a>
			</div>
		</div>
	</form>
{/block}
