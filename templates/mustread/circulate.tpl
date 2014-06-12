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
			<button class="select-members btn btn-default" data-id="{$item.itemId|escape}">{tr}Select Members{/tr}</button>
			<button class="btn btn-primary">{tr}Add all members{/tr}</button>
		</div>
	</form>
	<form method="post" class="no-ajax add-users" action="{service controller=mustread action=circulate_users id=$item.itemId}">
		<div class="alert alert-success hidden">
			<strong class="groupname"></strong>
			{tr}<span class="add-count">0</span> have been added. <span class="skip-count">0</span> were skipped.{/tr}
		</div>
		<ul class="user-list list-unstyled">
			<li class="empty">{tr}No members to select from.{/tr}</li>
		</ul>
		<div class="form-group">
			<button class="btn btn-primary">{tr}Add selected members{/tr}</button>
		</div>
	</form>
{/block}
