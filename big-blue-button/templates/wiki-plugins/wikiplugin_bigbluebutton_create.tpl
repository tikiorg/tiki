<p>{tr}Last time we checked, the room you requested did not exist.{/tr}</p>
{permission name=bigbluebutton_create}
	<form method="post" action="">
		<input type="hidden" name="bbb" value="{$bbb_name|escape}"/>
		<input type="submit" value="{tr}Create{/tr}"/>
	</form>
{/permission}
