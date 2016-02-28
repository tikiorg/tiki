{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="navigation"}
	<div class="form-group">
		{permission name=admin_trackers}
			<a class="btn btn-default" href="{service controller=tabular action=manage}">{icon name=list} {tr}Manage{/tr}</a>
			<a class="btn btn-default" href="{service controller=tabular action=create}">{icon name=create} {tr}New{/tr}</a>
		{/permission}
	</div>
{/block}

{block name="content"}
	{if $completed}
		{remarksbox type=confirm title="{tr}Import Completed{/tr}"}
			{tr}Your import was completed succesfully.{/tr}
		{/remarksbox}
	{else}
		<form class="no-ajax" method="post" action="{service controller=tabular action=import_csv tabularId=$tabularId}" enctype="multipart/form-data">
			<div class="form-group">
				<label class="control-label">{tr}File{/tr}</label>
				<input type="file" name="file" accept="text/csv">
			</div>
			<div class="submit">
				<input class="btn btn-primary" type="submit" value="{tr}Import{/tr}">
			</div>
		</form>
	{/if}
{/block}
