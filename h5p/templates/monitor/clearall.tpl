{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<form method="post" action="{service controller=monitor action=clearall timestamp=$timestamp}">
		<input type="submit" value="{tr}Mark all as read{/tr}" class="btn btn-primary">
	</form>
{/block}
