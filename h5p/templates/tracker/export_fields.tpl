{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form method="post" action="">
	<div class="form-group">
		<label for="export" class="control-label">{tr}Export{/tr}</label>
		<textarea rows="20" name="export" class="form-control">{$export|escape}</textarea>
	</div>
</form>
{/block}
