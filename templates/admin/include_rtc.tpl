{* $Id$ *}
<form class="form-horizontal" action="tiki-admin.php?page=rtc" method="post">
	{ticket}
	<div class="t_navbar margin-bottom-md">
		{button href="tiki-admingroups.php" _class="btn-link tips" _type="text" _icon_name="group" _text="{tr}Groups{/tr}" _title=":{tr}Group Administration{/tr}"}
		{button href="tiki-adminusers.php" _class="btn-link tips" _type="text" _icon_name="user" _text="{tr}Users{/tr}" _title=":{tr}User Administration{/tr}"}
		{permission_link addclass="btn btn-link" _type="text" mode=text label="{tr}Permissions{/tr}"}
		<a href="{service controller=managestream action=list}" class="btn btn-link tips">{tr}Activity Rules{/tr}</a>
		{include file='admin/include_apply_top.tpl'}
	</div>
	{tabset name="admin_rtc"}
		{tab name="{tr}BigBlueButton{/tr}"}
			<br>
			{preference name=bigbluebutton_feature}
			<div class="adminoptionboxchild" id="bigbluebutton_feature_childcontainer">
				{preference name=bigbluebutton_server_location}
				{preference name=bigbluebutton_server_salt}
				{preference name=bigbluebutton_recording_max_duration}
				{preference name=wikiplugin_bigbluebutton}
			</div>
		{/tab}
		{tab name="XMPP"}
			<h2>XMPP</h2>
			{preference name=xmpp_feature}
			<div class="adminoptionboxchild" id="xmpp_feature_childcontainer">
				{preference name=xmpp_server_host}
				{preference name=xmpp_server_http_bind}
				{preference name=xmpp_openfire_use_token}
			</div>
		{/tab}
	{/tabset}
	{include file='admin/include_apply_bottom.tpl'}
</form>
