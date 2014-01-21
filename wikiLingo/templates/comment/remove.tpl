{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{if $status neq 'DONE'}
	<form method="post" action="{service controller=comment action=remove}">
		<p>{tr}Are you sure you want to remove this comment?{/tr}</p>
		<div>
			{$parsed}
		</div>
		<p>
			<input type="hidden" name="threadId" value="{$threadId|escape}"/>
			<input type="hidden" name="confirm" value="1"/>
			<input type="submit" class="btn btn-default btn-sm" value="{tr}Confirm{/tr}"/>
		</p>
	</form>
{/if}
{object_link type=$objectType id=$objectId title="{tr}Return{/tr}"}
{/block}
