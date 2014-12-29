{tr}Meeting ID:{/tr} {$bbb_meeting|escape}
{permission type=bigbluebutton object=$bbb_meeting name=tiki_p_assign_perm_bigbluebutton}
	{permission_link mode=button type=bigbluebutton id=$bbb_meeting title=$bbb_meeting}
{/permission}
<p>{tr}Last time we checked, the room you requested did not exist.{/tr}</p>
{permission name=bigbluebutton_create type=bigbluebutton object=$bbb_meeting}
	<form target="_blank" method="post" action="{service controller=bigbluebutton action=join}">
		<input type="hidden" name="params" value="{$bbb_params|escape}">
		<input type="submit" class="button btn btn-default" value="{tr}Create{/tr}">
	</form>
	{include file="wiki-plugins/wikiplugin_bigbluebutton_view_recordings.tpl"}
{/permission}
