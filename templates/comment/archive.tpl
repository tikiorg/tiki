{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $status neq 'DONE'}
		<form method="post" action="{service controller="comment" action="archive"}">
			<div class="panel panel-default">
				<div class="panel-heading">	
					{if $do eq 'archive'}
						{tr}Are you sure you want to archive this comment?{/tr}
					{else}
						{tr}Are you sure you want to unarchive this comment?{/tr}
					{/if}
				</div>
				<div class="panel-body">
					<input type="hidden" name="do" value="{$do|escape}">
					<input type="hidden" name="threadId" value="{$threadId|escape}">
					<input type="hidden" name="confirm" value="1">
					<input type="submit" class="btn btn-primary btn-sm" value="{tr}Confirm{/tr}">
					{object_link type=$type id=$objectId title="{tr}Cancel{/tr}"}
				</div>
			</div>
		</form>
	{/if}
{/block}
