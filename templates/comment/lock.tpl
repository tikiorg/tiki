{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $status neq 'DONE'}
		<form method="post" action="{service controller="comment" action="lock"}">
			<div class="form-group">
				{tr}Are you sure you want to lock comments on this object?{/tr}
			</div>
			<div class="submit">
				<input type="hidden" name="type" value="{$type|escape}"/>
				<input type="hidden" name="objectId" value="{$objectId|escape}"/>
				<input type="hidden" name="confirm" value="1"/>
				<input type="submit" class="btn btn-primary" value="{tr}Confirm{/tr}"/>
			</div>
		</form>
	{/if}
{/block}
