{if $status neq 'DONE'}
	<form method="post" action="tiki-ajax_services.php">
		<p>{tr}Are you sure you want to lock comments on this object?{/tr}</p>
		<p>
			<input type="hidden" name="controller" value="comment"/>
			<input type="hidden" name="action" value="lock"/>
			<input type="hidden" name="type" value="{$type|escape}"/>
			<input type="hidden" name="objectId" value="{$objectId|escape}"/>
			<input type="hidden" name="confirm" value="1"/>
			<input type="submit" value="{tr}Confirm{/tr}"/>
		</p>
	</form>
{/if}
{object_link type=$type id=$objectId title="{tr}Return{/tr}"}
