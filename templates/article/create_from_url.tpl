{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	{if $id}
		<div class="alert alert-success">
			<strong>{tr}Your article has been added!{/tr}</strong>
			{object_link type=article id=$id}
		</div>
	{/if}
	<form method="post" action="{service controller=article action=create_from_url}">
		<div class="form-group">
			<label for="url" class="control-label">{tr}URL{/tr}</label>
			<input type="url" name="url" class="form-control">
		</div>
		
		<div class="submit">
			<input class="btn btn-primary" type="submit" value="{tr}Create Article{/tr}">
		</div>
	</form>
{/block}

