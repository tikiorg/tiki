{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form method="post" action="{service controller=workspace action=add_template}">
	<label>
		{tr}Name{/tr}
		<input type="text" name="name">
	</label>

	<div class="submit">
		<input type="submit" class="btn btn-default btn-sm" value="{tr}Add template{/tr}">
	</div>
</form>
{/block}
