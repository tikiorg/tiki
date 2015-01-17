{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="navigation"}
	<div class="form-group">
		{permission name=admin_trackers}
			<a class="btn btn-default" href="{service controller=tabular action=manage}">{icon name=list} {tr}Manage{/tr}</a>
		{/permission}
	</div>
{/block}

{block name="content"}
	<form class="form-horizontal" method="post" action="{service controller=tabular action=create}">
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Name{/tr}</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" name="name" required>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Tracker{/tr}</label>
			<div class="col-sm-9">
				{object_selector _class="form-control" type="tracker" _simplename="trackerId"}
			</div>
		</div>
		<div class="form-group submit">
			<div class="col-sm-9 col-sm-push-3">
				<input type="submit" class="btn btn-primary" value="{tr}Create{/tr}">
			</div>
		</div>
	</form>
{/block}
