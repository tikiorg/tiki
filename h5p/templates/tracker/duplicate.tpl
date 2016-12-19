{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<form class="form" name="duplicate_tracker" action="{service controller=tracker action=duplicate}" method="post">
		<div class="form-group">
			<label class="control-label" for="name">
				{tr}Name{/tr}
			</label>
			<input type="text" name="name" id="name" class="form-control" placeholder="Name of the new tracker" required="required">
		</div>
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
		{if $prefs.feature_categories eq 'y'}
			<div class="checkbox">
				<label>
					<input type="checkbox" name="dupCateg" value="1">{tr}Duplicate categories{/tr}
				</label>
			</div>
		{/if}
		<div class="checkbox">
			<label>
				<input type="checkbox" name="dupPerms" value="1">{tr}Duplicate permissions{/tr}
			</label>
		</div>
		<div class="submit text-center">
			<input type="hidden" name="confirm" value="1">
			<input type="submit" class="btn btn-primary" value="{tr}Duplicate{/tr}">
		</div>
	</form>
{/block}
