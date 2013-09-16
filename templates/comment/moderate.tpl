{if $status neq 'DONE'}
	<form method="post" action="{service controller=comment action=moderate}">
		{if $do eq 'approve'}
			<p>{tr}Are you sure you want to approve this comment?{/tr}</p>
		{else}
			<p>{tr}Are you sure you want to reject this comment?{/tr}</p>
		{/if}
		<p>
			<input type="hidden" name="do" value="{$do|escape}"/>
			<input type="hidden" name="threadId" value="{$threadId|escape}"/>
			<input type="hidden" name="confirm" value="1"/>
			<input type="submit" class="btn btn-default" value="{tr}Confirm{/tr}"/>
		</p>
	</form>
{/if}
{object_link type=$type id=$objectId title="{tr}Return{/tr}"}
