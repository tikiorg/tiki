{if $status neq 'DONE'}
	<form method="post" action="tiki-ajax_services.php">
		<p>{tr}Are you sure you want to remove this comment?{/tr}</p>
		<div>
			{$parsed}
		</div>
		<p>
			<input type="hidden" name="controller" value="comment"/>
			<input type="hidden" name="action" value="remove"/>
			<input type="hidden" name="threadId" value="{$threadId|escape}"/>
			<input type="hidden" name="confirm" value="1"/>
			<input type="submit" value="{tr}Confirm{/tr}"/>
		</p>
	</form>
{/if}
{object_link type=$objectType id=$objectId title="{tr}Return{/tr}"}
