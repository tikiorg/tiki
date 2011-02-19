<div class="navbar">

{if $prefs.feature_userPreferences eq 'y'}
	{button _icon="img/mytiki/mytiki.gif" _text="{tr}MyTiki{/tr}" href="tiki-my_tiki.php" _menu_text="y"}
{/if}

{if $prefs.feature_userPreferences eq 'y' or $prefs.change_password eq 'y'}
	{button _icon="img/mytiki/prefs.gif" _text="{tr}Preferences{/tr}" href="tiki-user_preferences.php" _menu_text="y"}
{/if}

{button _icon="img/mytiki/admin.gif" _text="{tr}MyInfo{/tr}" href="tiki-user_information.php" _menu_text="y"}

{if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
	{if $unread}
		{button _icon="img/mytiki/messages.gif" _text="{tr}Messages{/tr} ($unread)" href="messu-mailbox.php" _menu_text="y"}
	{else}
		{button _icon="img/mytiki/messages.gif" _text="{tr}Messages{/tr}" href="messu-mailbox.php" _menu_text="y"}
	{/if}
{/if}

{if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
	{button _icon="img/mytiki/tasks.gif" _text="{tr}Tasks{/tr}" href="tiki-user_tasks.php" _menu_text="y"}
{/if}

{if $prefs.feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
	{button _icon="img/mytiki/bookmarks.gif" _text="{tr}Bookmarks{/tr}" href="tiki-user_bookmarks.php" _menu_text="y"}
{/if}

{if $prefs.user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
	{button _icon="img/mytiki/modules.gif" _text="{tr}Modules{/tr}" href="tiki-user_assigned_modules.php" _menu_text="y"}
{/if}

{if $prefs.feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
	{button _icon="img/mytiki/webmail.gif" _text="{tr}Webmail{/tr}" href="tiki-webmail.php" _menu_text="y"}
{/if}

{if $prefs.feature_contacts eq 'y'}
	{button _icon="img/mytiki/stock_contact.png" _text="{tr}Contacts Preferences{/tr}" href="tiki-user_contacts_prefs.php" _menu_text="y"}
{/if}

{if $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
	{button _icon="img/mytiki/notes.gif" _text="{tr}Notepad{/tr}" href="tiki-notepad_list.php" _menu_text="y"}
{/if}

{if $prefs.feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
	{button _icon="img/mytiki/myfiles.gif" _text="{tr}MyFiles{/tr}" href="tiki-userfiles.php" _menu_text="y"}
{/if}

{if $prefs.feature_minical eq 'y' and $tiki_p_minical eq 'y'}
	{button _icon="img/mytiki/minical.gif" _text="{tr}Mini Calendar{/tr}" href="tiki-minical.php" _menu_text="y"}
{/if}

{if $prefs.feature_user_watches eq 'y'}
	{button _icon="img/mytiki/mywatches.gif" _text="{tr}My Watches{/tr}" href="tiki-user_watches.php" _menu_text="y"}
{/if}

{if $prefs.feature_actionlog == 'y' and !empty($user) and ($tiki_p_view_actionlog eq 'y' || $tiki_p_view_actionlog_owngroups eq 'y')}
	{button _icon="img/mytiki/gnome-vumeter.png" _text="{tr}Action Log{/tr}" href="tiki-admin_actionlog.php?selectedUsers[]=$user" _menu_text="y"}
{/if}

{if $prefs.feature_socialnetworks == 'y' and !empty($user) and ($tiki_p_socialnetworks eq 'y' or $tiki_p_admin_socialnetworks eq 'y')}
	{button _icon="pics/large/socialnetworks.png" _text="{tr}Social networks{/tr}" href="tiki-socialnetworks.php" _menu_text="y"}
{/if}

</div>
<br/>
