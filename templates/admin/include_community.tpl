{* $Id$ *}

<div class="navbar">
	{button href="tiki-admingroups.php" _text="{tr}Admin Groups{/tr}"}
	{button href="tiki-adminusers.php" _text="{tr}Admin Users{/tr}"}
	{button href="tiki-objectpermissions.php" _text="{tr}Manage permissions{/tr}"}	
</div>

<form action="tiki-admin.php?page=community" method="post">
	<div class="input_submit_container clear" style="text-align: right;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="admin_community"}
		{tab name="{tr}User features{/tr}"}
	
			<div class="admin featurelist">
				{preference name=feature_score}
				{preference name=feature_mytiki}
				{preference name=feature_minical}
				{preference name=feature_userPreferences}
				{preference name=feature_notepad}
				{preference name=feature_user_bookmarks}
				{preference name=feature_contacts}
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
				{preference name=feature_invite}
			</div>
		{/tab}
		{tab name="{tr}Social Network{/tr}"}
			<fieldset class="admin">
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

			<fieldset class="admin">
				<legend>{tr}Activity Stream{/tr}</legend>
				{preference name=activity_basic_events visible="always"}

				<div class="adminoptionboxchild" id="activity_basic_events_childcontainer">
					{preference name=activity_basic_tracker_create}
					{preference name=activity_basic_tracker_update}
					{preference name=activity_basic_user_follow_add}
					{preference name=activity_basic_user_follow_incoming}
					{preference name=activity_basic_user_friend_add}
				</div>
				{preference name=activity_custom_events visible="always"}

				<div class="adminoptionboxchild" id="activity_custom_events_childcontainer">
					
					{$headerlib->add_dracula()}
					{$headerlib->add_jsfile('lib/jquery_tiki/activity.js', 'external')}
					<div id="graph-canvas" class="graph-canvas" data-graph-nodes="{$event_graph.nodes|@json_encode|escape}" data-graph-edges="{$event_graph.edges|@json_encode|escape}"></div>
					<div><button href="#" id="graph-draw" class="button">{tr}Draw Event Diagram{/tr}</button></div>
					<div><button href="{service controller=managestream action=list}" id="show-rules">{tr}Show Rules{/tr}</button></div>
					{jq}
					$('#graph-draw').click(function(e) {
						$('#graph-canvas')
							.empty()
							.css('width', $window.width() - 50)
							.css('height', $window.height() - 130)
							.dialog({
								title: "Events",
								width: $window.width() - 20,
								height: $window.height() - 100
							})
							.drawGraph();
						return false;
					});
					{/jq}
				</div>
			</fieldset>
		{/tab}
		
		{tab name="{tr}Plugins{/tr}"}
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
			{preference name=user_show_realnames}
			{preference name=user_in_search_result}
			{preference name=highlight_group}
			{preference name=feature_display_my_to_others}
			{preference name=user_tracker_infos}
			{preference name=user_use_gravatar}

			{preference name=user_who_viewed_my_stuff}
			{preference name=user_who_viewed_my_stuff_days}
			{preference name=user_who_viewed_my_stuff_show_others}
			
			{preference name=user_store_file_gallery_picture}
			{preference name=user_picture_gallery_id}
			{preference name=user_default_picture_id}
			
			<input type="hidden" name="userfeatures" />
			<fieldset>
				<legend>{tr}Community{/tr}{help url="Community"}</legend>
				{preference name=feature_community_gender}
				{preference name=feature_community_mouseover}
				<div class="adminoptionboxchild" id="feature_community_mouseover_childcontainer">
					{preference name=feature_community_mouseover_name}
					{preference name=feature_community_mouseover_gender}
					{preference name=feature_community_mouseover_picture}
					{preference name=feature_community_mouseover_friends}
					{preference name=feature_community_mouseover_score}
					{preference name=feature_community_mouseover_country}
					{preference name=feature_community_mouseover_email}
					{preference name=feature_community_mouseover_lastlogin}
					{preference name=feature_community_mouseover_distance}
				</div>
			</fieldset>
			
			<fieldset>
				<legend>{tr}Email notifications to group leaders when users join/leave a group{/tr}{help url="Community"}</legend>
				{preference name=feature_community_Strings_to_ignore}
				{preference name=feature_community_String_to_append}
				{preference name=feature_community_send_mail_join}
				{preference name=feature_community_send_mail_leave}
			</fieldset>

			{* ************ Users Default Preferences *}
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

			{* *** User Messages *** *}
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
				<legend>{tr}My Tiki{/tr}</legend>
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
			{preference name=bigbluebutton_feature}
			<div class="adminoptionboxchild" id="bigbluebutton_feature_childcontainer">
				{preference name=bigbluebutton_server_location}
				{preference name=bigbluebutton_server_salt}
				{preference name=bigbluebutton_recording_max_duration}
				{preference name=wikiplugin_bigbluebutton}
			</div>
		{/tab}
	{/tabset}
	<div class="input_submit_container clear" style="text-align: center;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
