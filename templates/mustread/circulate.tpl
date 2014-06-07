{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<form method="post" class="no-ajax add-members" action="{service controller=mustread action=circulate_members id=$item.itemId}">
		<div class="alert alert-success hidden">
			<strong class="groupname"></strong>
			{tr}<span class="add-count">0</span> have been added. <span class="skip-count">0</span> were skipped.{/tr}
		</div>
		<div class="form-group">
			<label for="group" class="control-label">{tr}Group{/tr}</label>
			<input type="text" id="group" name="group" value="" class="form-control">
			{autocomplete element='#group' type='groupname'}
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-primary" value="{tr}Add all members{/tr}">
		</div>
	</form>
{/block}
