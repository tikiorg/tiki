{tr}Meeting ID:{/tr} {$bbb_meeting|escape} 
<p>{tr}Last time we checked, the room you requested did not exist.{/tr}</p>
{permission name=bigbluebutton_create type=bigbluebutton object=$bbb_meeting}
	<form target="_blank" method="post" action="">
		<input type="hidden" name="bbb" value="{$bbb_meeting|escape}"/>
		<input type="submit" class="button" value="{tr}Create{/tr}"/>
	</form>
{/permission}
