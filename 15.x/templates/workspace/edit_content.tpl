{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form class="simple workspace-ui-content-form" method="post" action="{service controller=workspace action=edit_content}">
	<label>
		{tr}Wiki Page Source{/tr}
		<input type="text" name="page" value="{$page|escape}"/>
	</label>
	<p><strong>{tr}OR{/tr}</strong></p>
	<label>
		{tr}Content{/tr}
		<textarea name="content">{$content|escape}</textarea>
	</label>
	<div class="submit">
		<input type="submit" class="btn btn-default btn-sm" value="{tr}Set{/tr}"/>
	</div>
</form>
{/block}
