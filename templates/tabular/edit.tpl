{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="navigation"}
	<div class="form-group">
		<a class="btn btn-default" href="{service controller=tabular action=manage}">{icon name=list} {tr}Manage{/tr}</a>
		<a class="btn btn-default" href="{service controller=tabular action=create}">{icon name=create} {tr}New{/tr}</a>
	</div>
{/block}

{block name="content"}
	<form class="form-horizontal" method="post" action="{service controller=tabular action=edit tabularId=$tabularId}">
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Name{/tr}</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" name="name" value="{$name|escape}" required>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Fields{/tr}</label>
			<div class="col-sm-9">
				<textarea name="fields" class="form-control">{$fields|escape}</textarea>
			</div>
		</div>
		<div class="form-group submit">
			<div class="col-sm-9 col-sm-push-3">
				<input type="submit" class="btn btn-primary" value="{tr}Update{/tr}">
			</div>
		</div>
	</form>
{/block}
