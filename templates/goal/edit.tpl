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
					<div class="radio col-md-offset-3">
						<label>
							<input type="radio" name="type" value="user" {if $goal.type neq 'group'}checked{/if}>
							{tr}Individual Goal{/tr}
						</label>
					</div>
					<div class="radio col-md-offset-3">
						<label>
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
		{/tabset}
		<div class="form-group">
			<div class="col-md-offset-3 col-md-9">
				<input type="submit" class="btn btn-primary" value="{tr}Save{/tr}">
				<a href="{service controller=goal action=admin}" class="btn btn-link">{tr}Cancel{/tr}</a>
			</div>
		</div>
	</form>
{/block}
