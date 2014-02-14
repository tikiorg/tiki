{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<form class="form-horizontal" method="post" action="{service controller=goal action=create}">
		<div class="form-group">
			<label for="name" class="control-label col-md-3">{tr}Name{/tr}</label>
			<div class="col-md-9">
				<input type="text" name="name" class="form-control" value="{$name|escape}">
			</div>
		</div>
		<div class="form-group">
			<label for="description" class="control-label col-md-3">{tr}Description{/tr}</label>
			<div class="col-md-9">
				<textarea name="description" class="form-control">{$description|escape}</textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-offset-3 col-md-9">
				<input type="submit" class="btn btn-primary" value="{tr}Create{/tr}">
			</div>
		</div>
	</form>
{/block}
