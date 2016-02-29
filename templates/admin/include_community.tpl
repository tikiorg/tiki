{* $Id$ *}
<form class="form-horizontal" action="tiki-admin.php?page=community" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="t_navbar margin-bottom-md">
		{button href="tiki-admingroups.php" _class="btn-link tips" _icon_name="group" _text="{tr}Groups{/tr}" _title=":{tr}Group Administration{/tr}"}
		{button href="tiki-adminusers.php" _class="btn-link tips" _icon_name="user" _text="{tr}Users{/tr}" _title=":{tr}User Administration{/tr}"}
		{permission_link addclass="btn btn-default btn-link" mode=text label="{tr}Permissions{/tr}"}
		<a href="{service controller=managestream action=list}" class="btn btn-default btn-link tips">{tr}Activity Rules{/tr}</a>
		<div class="pull-right">
			<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
		</div>
	</div>
	{tabset name="admin_community"}
		{tab name="{tr}User features{/tr}"}
			<h2>{tr}User features{/tr}</h2>
			<div class="admin featurelist">
				{preference name=feature_unified_user_details}
				{preference name=feature_score}
				{preference name=feature_mytiki}
				{preference name=feature_minical}
				{preference name=feature_userPreferences}
				{preference name=feature_notepad}
				{preference name=feature_user_bookmarks}
				{preference name=feature_contacts}
				{preference name=monitor_enabled}
				<div class="adminoptionboxchild" id="monitor_enabled_childcontainer">
					{preference name=monitor_individual_clear}
					{preference name=monitor_count_refresh_interval}
					{preference name=monitor_reply_email_pattern}
					{preference name=monitor_digest}
					{remarksbox type="info" title="{tr}Information{/tr}"}
						{tr}For the digest emails to be sent out, you will need to set-up a cron job.{/tr}</br>
						{tr}Adjust the command parameters for your digest frequency. Default frequency is 7 days.{/tr}</br>
						<strong>{tr}Sample command:{/tr}</strong>
						<code>/usr/bin/php {$monitor_command|escape}</code>
					{/remarksbox}
				</div>
				{preference name=feature_user_watches_translations}
				{preference name=feature_user_watches}
				{preference name=feature_group_watches}
				{preference name=feature_daily_report_watches}
				<div class="adminoptionboxchild" id="feature_daily_report_watches_childcontainer">
					{preference name=dailyreports_enabled_for_new_users}
				</div>
				{preference name=feature_user_watches_translations}
				{preference name=feature_usermenu}
				{preference name=feature_tasks}
				{preference name=feature_messages}
				{preference name=feature_userfiles}
				<div class="adminoptionboxchild" id="feature_userfiles_childcontainer">
					{preference name=feature_use_fgal_for_user_files}
				</div>
				{preference name=feature_webmail}
				{preference name=feature_userlevels}
				{preference name=feature_groupalert}
				{preference name=change_theme}
				{preference name=auth_token_tellafriend}
				{preference name=auth_token_share}
				{preference name=feature_wiki_userpage}
				<div class="adminoptionboxchild" id="feature_wiki_userpage_childcontainer">
					{preference name=feature_wiki_userpage_prefix}
				</div>
				{preference name=feature_group_transition}
				{preference name=user_favorites}
				{preference name=user_likes}
				{preference name=feature_invite}
				{preference name=feature_wizard_user}
				{preference name=mustread_enabled}
				<div class="adminoptionboxchild" id="mustread_enabled_childcontainer">
					{preference name=mustread_tracker}
				</div>
				{preference name=user_multilike_config}
			</div>
		{/tab}
		{tab name="{tr}Social Network{/tr}"}
			<h2>{tr}Social Network{/tr}</h2>
			<fieldset class="table">
				<legend>{tr}Friendship and Followers{/tr}</legend>
				{preference name=feature_friends}
				<div class="adminoptionboxchild" id="feature_friends_childcontainer">
					{preference name=social_network_type}
					<fieldset>
						<legend>{tr}Select which items to display when listing users{/tr}</legend>
						{preference name=user_list_order}
						{preference name=feature_community_list_name}
						{preference name=feature_community_list_score}
						{preference name=feature_community_list_country}
						{preference name=feature_community_list_distance}
					</fieldset>
				</div>
			</fieldset>
			<fieldset class="table">
				<legend>{tr}Activity Stream{/tr}</legend>
				{preference name=activity_basic_events}
				<div class="adminoptionboxchild" id="activity_basic_events_childcontainer">
					{preference name=activity_basic_tracker_create}
					{preference name=activity_basic_tracker_update}
					{preference name=activity_basic_user_follow_add}
					{preference name=activity_basic_user_follow_incoming}
					{preference name=activity_basic_user_friend_add}
				</div>
				{preference name=activity_custom_events}
				{preference name=activity_notifications}
			</fieldset>
			<fieldset>
				<legend>{tr}Goal, Recognition and Rewards{/tr}</legend>
				{preference name=goal_enabled}
				{preference name=goal_badge_tracker}
				{preference name=goal_group_blacklist}
			</fieldset>
		{/tab}
		{tab name="{tr}Plugins{/tr}"}
			<h2>{tr}Plugins{/tr}</h2>
			{preference name=wikiplugin_author}
			{preference name=wikiplugin_avatar}
			{preference name=wikiplugin_favorite}
			{preference name=wikiplugin_group}
			{preference name=wikiplugin_groupexpiry}
			{preference name=wikiplugin_invite}
			{preference name=wikiplugin_mail}
			{preference name=wikiplugin_map}
			{preference name=wikiplugin_memberlist}
			{preference name=wikiplugin_memberpayment}
			{preference name=wikiplugin_perm}
			{preference name=wikiplugin_proposal}
			{preference name=wikiplugin_realnamelist}
			{preference name=wikiplugin_subscribegroup}
			{preference name=wikiplugin_subscribegroups}
			{preference name=wikiplugin_topfriends}
			{preference name=wikiplugin_usercount}
			{preference name=wikiplugin_userlink}
			{preference name=wikiplugin_userlist}
			{preference name=wikiplugin_userpref}
		{/tab}
		{tab name="{tr}General Settings{/tr}"}
			<h2>{tr}General Settings{/tr}</h2>
			{preference name=user_show_realnames}
			{preference name=user_in_search_result}
			{preference name=highlight_group}
			{preference name=feature_display_my_to_others}
			{preference name=user_tracker_infos}
			{preference name=user_who_viewed_my_stuff}
			{preference name=user_who_viewed_my_stuff_days}
			{preference name=user_who_viewed_my_stuff_show_others}
			<fieldset>
				<legend>{tr}Profile picture{/tr}</legend>
					{preference name=user_use_gravatar}
					{preference name=user_store_file_gallery_picture}
					{preference name=user_small_avatar_size}
					{preference name=user_small_avatar_square_crop}
					{preference name=user_picture_gallery_id}
					{preference name=user_default_picture_id}
			</fieldset>
			<fieldset>
				<legend>{tr}Community{/tr}{help url="Community"}</legend>
				{preference name=feature_community_gender}
				{preference name=feature_community_mouseover}
				<div class="adminoptionboxchild" id="feature_community_mouseover_childcontainer">
					{preference name=feature_community_mouseover_name}
					{preference name=feature_community_mouseover_gender}
					{preference name=feature_community_mouseover_picture}
					{preference name=feature_community_mouseover_score}
					{preference name=feature_community_mouseover_country}
					{preference name=feature_community_mouseover_email}
					{preference name=feature_community_mouseover_lastlogin}
					{preference name=feature_community_mouseover_distance}
				</div>
			</fieldset>
			<fieldset>
				<legend>
					{tr}Default user preferences{/tr}
					{help url="UsersDefaultPrefs" desc="{tr}Users Default Preferences{/tr}"}
				</legend>
				{preference name=users_prefs_mailCharset}
				{preference name=users_prefs_userbreadCrumb}
				{preference name=users_prefs_display_timezone}
				{preference name=users_prefs_user_information}
				{preference name=users_prefs_user_dbl}
				{preference name=users_prefs_display_12hr_clock}
				{preference name=users_prefs_show_mouseover_user_info}
				{preference name=users_prefs_tasks_maxRecords}
				{preference name=users_prefs_diff_versions}
			</fieldset>
			<fieldset>
				<legend>
					{tr}User messages{/tr}
					{help url="Inter-User+Messages"}
				</legend>
				{preference name=users_prefs_mess_maxRecords}
				{preference name=users_prefs_allowMsgs}
				{preference name=users_prefs_mess_sendReadStatus}
				{preference name=users_prefs_minPrio}
				{preference name=users_prefs_mess_archiveAfter}
			</fieldset>
			<fieldset>
				<legend>{tr}My Account{/tr}</legend>
				{preference name=users_prefs_mytiki_pages}
				{preference name=users_prefs_mytiki_blogs}
				{preference name=users_prefs_mytiki_gals}
				{preference name=users_prefs_mytiki_msgs}
				{preference name=users_prefs_mytiki_tasks}
				{preference name=users_prefs_mytiki_forum_topics}
				{preference name=users_prefs_mytiki_forum_replies}
				{preference name=users_prefs_mytiki_items}
			</fieldset>
		{/tab}
		{tab name="{tr}BigBlueButton{/tr}"}
			<h2>{tr}BigBlueButton{/tr}</h2>
			{preference name=bigbluebutton_feature}
			<div class="adminoptionboxchild" id="bigbluebutton_feature_childcontainer">
				{preference name=bigbluebutton_server_location}
				{preference name=bigbluebutton_server_salt}
				{preference name=bigbluebutton_recording_max_duration}
				{preference name=wikiplugin_bigbluebutton}
			</div>
		{/tab}
	{/tabset}
	<div class="t_navbar margin-bottom-md text-center">
		<input type="hidden" name="userfeatures" />
		<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
	</div>
</form>
