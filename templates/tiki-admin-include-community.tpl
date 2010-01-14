{* $Id$ *}

<div class="navbar">
	{button href="tiki-admingroups.php" _text="{tr}Admin Groups{/tr}"}
	{button href="tiki-adminusers.php" _text="{tr}Admin Users{/tr}"}
</div>

<form action="tiki-admin.php?page=community" method="post">
	<div class="input_submit_container clear" style="text-align: right;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name="admin_community"}
		{tab name="{tr}User features{/tr}"}
			<div class="admin featurelist">
				{preference name=feature_mytiki}
				{preference name=feature_minical}
				{preference name=feature_userPreferences}
				{preference name=feature_notepad}
				{preference name=feature_user_bookmarks}
				{preference name=feature_contacts}
				{preference name=feature_user_watches}
				{preference name=feature_group_watches}
				{preference name=feature_daily_report_watches}
				{preference name=feature_user_watches_translations}
				{preference name=feature_usermenu}
				{preference name=feature_tasks}
				{preference name=feature_messages}
				{preference name=feature_userfiles}
				{preference name=feature_userlevels}
				{preference name=feature_groupalert}
				{preference name=change_theme}
			</div>
		{/tab}

		{tab name="{tr}General Settings{/tr}"}
			{preference name=user_show_realnames}
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="highlight_group">{tr}Highlight group{/tr}:</label>
					<select name="highlight_group" id="highlight_group">
						<option value="0">{tr}None{/tr}</option>
						{foreach key=g item=gr from=$listgroups}
							<option value="{$gr.groupName|escape}" {if $gr.groupName eq $prefs.highlight_group} selected="selected"{/if}>{$gr.groupName|truncate:"52":" ..."}</option>
						{/foreach}
					</select>
					{help url="Groups"}
				</div>
			</div>
			{preference name=feature_display_my_to_others}
			{preference name=user_tracker_infos}
			<em>{tr}Use the format: trackerId, fieldId1, fieldId2, ...{/tr}</em>

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

			<input type="hidden" name="users_defaults" />
			{* ************ Users Default Preferences *}
			<fieldset>
				<legend>
					{tr}Default user preferences{/tr}
					{help url="UsersDefaultPrefs" desc="{tr}Users Default Preferences{/tr}"}
				</legend>
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="users_prefs_mailCharset">{tr}Character set for mail{/tr}: </label>
					<select name="users_prefs_mailCharset" id="users_prefs_mailCharset">
						<option value=''>{tr}default{/tr}</option>
						{section name=ix loop=$mailCharsets}
							<option value="{$mailCharsets[ix]|escape}" {if $prefs.users_prefs_mailCharset eq $mailCharsets[ix]|escape}selected="selected"{/if}>{$mailCharsets[ix]}</option>
						{/section}
					</select>
				</div>
			</div>
			{if $prefs.change_theme eq 'y'}
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="users_prefs_theme">{tr}Theme{/tr}:</label>
						<select name="users_prefs_theme" id="users_prefs_theme">
							<option value='' >{tr}default{/tr}</option>
							{section name=ix loop=$styles}
								{if count($prefs.available_styles) == 0 || in_array($styles[ix], $prefs.available_styles)}
									<option value="{$styles[ix]|escape}" {if $users_prefs_theme eq $styles[ix]|escape}selected="selected"{/if}>{$styles[ix]}</option>
								{/if}
							{/section}
						</select>
					</div>
				</div>
			{/if}
			{if $prefs.change_language eq 'y'}
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="users_prefs_language">{tr}Language{/tr}:</label>
						<select name="users_prefs_language" id="users_prefs_language">
							<option value=''>{tr}default{/tr}</option>
							{section name=ix loop=$languages}
								{if count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
									<option value="{$languages[ix].value|escape}"	{if $users_prefs_language eq $languages[ix].value}selected="selected"{/if}>{$languages[ix].name}</option>
								{/if}
							{/section}
						</select>
					</div>
				</div>
			{/if}

			{preference name=users_prefs_userbreadCrumb}
			{preference name=users_prefs_display_timezone}
			{preference name=users_prefs_user_information}
			{preference name=users_prefs_user_dbl}

			{* not used (r18323)
				{if $prefs.feature_history eq 'y'}
					{preference name=users_prefs_diff_versions}
				{/if}
			*}

			{preference name=users_prefs_show_mouseover_user_info}
			{preference name=users_prefs_tasks_maxRecords}
		</fieldset>

		{* *** User Messages *** *}
		<fieldset>
			<legend>
				{tr}User messages{/tr}
				{help url="Inter-User+Messages"}
			</legend>

			{preference name=feature_messages}
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

	{tab name="{tr}Friendship Network{/tr}"}
		{preference name=feature_friends}
		<div class="adminoptionboxchild" id="feature_friends_childcontainer">
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					{tr}Select which items to display when listing users{/tr}.
				</div>
			</div>
		
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="user_list_order">{tr}Sort order{/tr}:</label>
					<select name="user_list_order" id="user_list_order">
						{if $prefs.feature_community_list_score eq 'y'}
							<option value="score_asc" {if $prefs.user_list_order=="score_asc"}selected="selected"{/if}>{tr}Score ascending{/tr}</option>
							<option value="score_desc" {if $prefs.user_list_order=="score_desc"}selected="selected"{/if}>{tr}Score descending{/tr}</option>
						{/if}
						{if $prefs.feature_community_list_name eq 'y'}
							<option value="pref:realName_asc" {if $prefs.user_list_order=="pref:realName_asc"}selected="selected"{/if}>{tr}Name ascending{/tr}</option>
							<option value="pref:realName_desc" {if $prefs.user_list_order=="pref:realName_desc"}selected="selected"{/if}>{tr}Name descending{/tr}</option>
						{/if}
						<option value="login_asc" {if $prefs.user_list_order=="login_asc"}selected="selected"{/if}>{tr}Login ascending{/tr}</option>
						<option value="login_desc" {if $prefs.user_list_order=="login_desc"}selected="selected"{/if}>{tr}Login descending{/tr}</option>
					</select>
				</div>
			</div>

			{preference name=feature_community_list_name}
			{preference name=feature_community_list_score}
			{preference name=feature_community_list_country}
			{preference name=feature_community_list_distance}
		</div>

		{/tab}
	{/tabset}
	<div class="input_submit_container clear" style="text-align: center;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
