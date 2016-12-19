{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	{if $removed}
		<div class="alert alert-success">{tr _0=$goal.name}%0 was removed.{/tr}</div>
	{else}
		<form method="post" action="{service controller=goal action=delete goalId=$goal.goalId}">
			<p>{tr _0=$goal.name}Are you sure you want to delete %0?{/tr}</p>
			<div class="submit">
				<input type="submit" class="btn btn-danger" value="{tr}Delete{/tr}">
			</div>
		</form>
	{/if}
{/block}
