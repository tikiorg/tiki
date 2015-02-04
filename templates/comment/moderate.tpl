{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $status neq 'DONE'}
		<form method="post" action="{service controller=comment action=moderate}" class="form">
			<div class="panel panel-default">
				<div class="panel-body form-group">
					{if $do eq 'approve'}
						{tr}Are you sure you want to approve this comment?{/tr}
					{else}
						{tr}Are you sure you want to reject this comment?{/tr}
					{/if}
				</div>
				<div class="panel-footer submit">
					<input type="hidden" name="do" value="{$do|escape}"/>
					<input type="hidden" name="threadId" value="{$threadId|escape}"/>
					<input type="hidden" name="confirm" value="1"/>
					<input type="submit" class="btn btn-primary btn-sm" value="{tr}Confirm{/tr}"/>
					{object_link type=$type id=$objectId title="{tr}Cancel{/tr}"}
				</div>
			</div>
		</form>
	{/if}
{/block}
