<div class="t_navbar btn-block form-group spacer-bottom-15px">

	{if $prefs.feature_userPreferences eq 'y'}
		{button _icon_name="home" class="btn btn-default" _text="{tr}My Account{/tr}" href="tiki-my_tiki.php"}
	{/if}

	{if $prefs.feature_userPreferences eq 'y' or $prefs.change_password eq 'y'}
		{button _icon_name="administer" class="btn btn-default" _text="{tr}Preferences{/tr}" href="tiki-user_preferences.php"}
	{/if}

	{button _icon_name="info" class="btn btn-default" _text="{tr}MyInfo{/tr}" href="tiki-user_information.php"}

	{if $prefs.feature_user_watches eq 'y'}
		{button _icon_name="watch" _text="{tr}My Watches{/tr}" href="tiki-user_watches.php"}
	{/if}

	<div class="btn-group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{tr}More{/tr} <span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
			{if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
				{if $unread}
					<li>{button _icon_name="admin_messages" class="btn btn-default" _text="{tr}Messages{/tr} ($unread)" href="messu-mailbox.php"}</li>
				{else}
					<li>{button _icon_name="admin_messages" class="btn btn-default" _text="{tr}Messages{/tr}" href="messu-mailbox.php"}</li>
				{/if}
			{/if}

			{if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
				<li>{button _icon_name="tasks" _text="{tr}Tasks{/tr}" href="tiki-user_tasks.php"}</li>
			{/if}

			{if $prefs.feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
				<li>{button _icon_name="book" _text="{tr}Bookmarks{/tr}" href="tiki-user_bookmarks.php"}</li>
			{/if}

			{if $prefs.user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
				<li>{button _icon_name="admin_module" _text="{tr}Modules{/tr}" href="tiki-user_assigned_modules.php"}</li>
			{/if}

			{if $prefs.feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
				<li>{button _icon_name="admin_webmail" _text="{tr}Webmail{/tr}" href="tiki-webmail.php"}</li>
			{/if}

			{if $prefs.feature_contacts eq 'y'}
				<li>{button _icon_name="user" _text="{tr}Contacts Preferences{/tr}" href="tiki-user_contacts_prefs.php"}</li>
			{/if}

			{if $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
				<li>{button _icon_name="notepad" _text="{tr}Notepad{/tr}" href="tiki-notepad_list.php"}</li>
			{/if}

			{if $prefs.feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
				<li>{button _icon_name="files" _text="{tr}MyFiles{/tr}" href="tiki-userfiles.php"}</li>
			{/if}

			{if $prefs.feature_minical eq 'y' and $tiki_p_minical eq 'y'}
				<li>{button _icon_name="calendar" _text="{tr}Mini Calendar{/tr}" href="tiki-minical.php"}</li>
			{/if}


			{if $prefs.feature_actionlog == 'y' and !empty($user) and ($tiki_p_view_actionlog eq 'y' || $tiki_p_view_actionlog_owngroups eq 'y')}
				<li>{button _icon_name="calendar" _text="{tr}Action Log{/tr}" href="tiki-admin_actionlog.php?selectedUsers[]=$user"}</li>
			{/if}

			{if $prefs.feature_socialnetworks == 'y' and !empty($user) and ($tiki_p_socialnetworks eq 'y' or $tiki_p_admin_socialnetworks eq 'y')}
				<li>{button _icon_name="admin_socialnetworks" _text="{tr}Social networks{/tr}" href="tiki-socialnetworks.php"}</li>
			{/if}

			{if $prefs.feature_mailin eq 'y' and !empty($user) and ($tiki_p_send_mailin eq 'y' or $tiki_p_admin_mailin eq 'y')}
				<li>{button _icon_name="mail-forward" _text="{tr}Mail-in{/tr}" href="tiki-user_mailin.php"}</li>
			{/if}
		</ul>
	</div>
</div>
