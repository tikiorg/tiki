{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<form method="post" action="{service controller=translation action=detach}" class="form" role="form">
		{tr}Are you sure you want to detach these translations?{/tr}
		<ul>
			<li>{object_link type=$type id=$source}</li>
			{if $source neq $target}
				<li>{object_link type=$type id=$target}</li>
			{/if}
		</ul>
		<div class="submit">
			<input type="hidden" name="type" value="{$type|escape}">
			<input type="hidden" name="source" value="{$source|escape}">
			<input type="hidden" name="target" value="{$target|escape}">
			<input type="hidden" name="confirm" value="1">
			<input type="submit" class="btn btn-primary" value="{tr}Confirm{/tr}">
		</div>
	</form>
{/block}
