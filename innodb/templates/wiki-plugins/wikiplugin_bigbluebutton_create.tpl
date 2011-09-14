<h2>{$bbb_meeting|escape}</h2>
<p>{tr}Last time we checked, the room you requested did not exist.{/tr}</p>
{permission name=bigbluebutton_create type=bigbluebutton object=$bbb_meeting}
	<form method="post" action="">
		<input type="hidden" name="bbb" value="{$bbb_meeting|escape}"/>
		<input type="submit" value="{tr}Create{/tr}"/>
	</form>
{/permission}
