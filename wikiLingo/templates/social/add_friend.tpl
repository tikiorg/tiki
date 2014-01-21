{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<p>{tr}Know the username?{/tr}</p>
<form method="post" action="{service controller=social action=add_friend}">
	<p>
		<input type="text" name="username" value="{$username|escape}"/>
		<input type="submit" class="btn btn-default btn-sm" value="{tr}Add{/tr}"/>
	</p>
</form>
{/block}
