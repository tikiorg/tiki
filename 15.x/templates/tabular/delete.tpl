{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form method="post" action="{service controller="tabular" action="delete" tabularId=$tabularId}">
	<p>{tr}Are you sure you want to delete this item?{/tr}</p>
	<div class="submit">
		<input type="submit" class="btn btn-danger" value="{tr}Delete item{/tr}">
	</div>
</form>
{/block}
