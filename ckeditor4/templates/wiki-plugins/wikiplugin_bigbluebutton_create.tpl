{tr}Meeting ID:{/tr} {$bbb_meeting|escape} 
<p>{tr}Last time we checked, the room you requested did not exist.{/tr}</p>
{permission name=bigbluebutton_create type=bigbluebutton object=$bbb_meeting}
	<form target="_blank" method="post" action="{service controller=bigbluebutton action=join}">
		<input type="hidden" name="params" value="{$bbb_params|escape}">
		<input type="submit" class="button" value="{tr}Create{/tr}">
	</form>
	{include file="wiki-plugins/wikiplugin_bigbluebutton_view_recordings.tpl"}
{/permission}
