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
			<label for="required_action" class="control-label">{tr}Required Action{/tr}</label>
			<select name="required_action" class="form-control" data-copy-into="#selection-action">
				{foreach $actions as $action => $label}
					<option value="{$action|escape}">{$label|escape}</option>
				{/foreach}
			</select>
		</div>
		<div class="form-group">
			<label for="group" class="control-label">{tr}Group{/tr}</label>
			{object_selector _simplename=group type=group _simpleclass="group-field" _placeholder="{tr}Group{/tr}"}
		</div>
		<div class="form-group">
			<button class="btn btn-primary">{tr}Add all members{/tr}</button>
		</div>
		<p class="lead">{tr}OR Select individuals...{/tr}</p>
	</form>
	<form method="post" class="no-ajax add-users" action="{service controller=mustread action=circulate_users id=$item.itemId}">
		<div class="alert alert-success hidden">
			<strong class="groupname"></strong>
			{tr}<span class="add-count">0</span> have been added. <span class="skip-count">0</span> were skipped.{/tr}
		</div>
		{object_selector_multi _simplename=users type=user _threshold=-1 _class="user-selector" _separator=";" _placeholder="{tr}Name{/tr}"}
		<div class="form-group">
			<button class="btn btn-primary">{tr}Add selected members{/tr}</button>
			<input id="selection-action" name="required_action" type="hidden" value="required">
		</div>
	</form>
{/block}
