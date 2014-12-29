{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $status neq 'DONE'}
		<form method="post" action="{service controller=comment action=unlock}">
			<p>{tr}Are you sure you want to unlock comments on this object?{/tr}</p>
			<p>
				<input type="hidden" name="type" value="{$type|escape}"/>
				<input type="hidden" name="objectId" value="{$objectId|escape}"/>
				<input type="hidden" name="confirm" value="1"/>
				<input type="submit" class="btn btn-default btn-sm" value="{tr}Confirm{/tr}"/>
			</p>
		</form>
	{/if}
	{object_link type=$type id=$objectId title="{tr}Return{/tr}"}
{/block}
