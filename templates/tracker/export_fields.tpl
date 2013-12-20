{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form class="simple" method="post" action="">
	<label>
		{tr}Export{/tr}
		<textarea rows="20" name="export">{$export|escape}</textarea>
	</label>
</form>
{/block}
