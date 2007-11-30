<form name="um">
<b>{$user}</b>
{if $prefs.feature_usermenu eq 'y'}
<select name="umenu" onchange="go(this);">
<option value="">{tr}User Bookmarks{/tr}</option>
<option value="">----</option>
{section name=ix loop=$usr_user_menus}
<option value="{$usr_user_menus[ix].url}">{$usr_user_menus[ix].name}</option>
{/section}
<option value="">----</option>
<option value="tiki-usermenu.php?url={$smarty.server.REQUEST_URI|escape:"url"}">{tr}Add{/tr} ...</option>
</select>
{/if}
<select name="mytiki" onchange="go(this);">
<option value="">{tr}MyTiki{/tr}</option>
<option value="">----</option>
{if $prefs.feature_userPreferences eq 'y'}
<option value="tiki-user_preferences.php">{tr}Preferences{/tr}</option>
{/if}
{if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<option value="messu-mailbox.php">{tr}Messages{/tr}</option>
{/if}
{if $prefs.feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
<option value="tiki-userfiles.php">{tr}User files{/tr}</option>
{/if}
{if $prefs.feature_minical eq 'y'}
<option value="tiki-minical.php">{tr}Calendar{/tr}</option>
{/if}
{if $prefs.feature_usermenu eq 'y'}
<option value="tiki-usermenu.php">{tr}Favorites{/tr}</option>
{/if}
{if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
<option value="tiki-user_tasks.php">{tr}Tasks{/tr}</option>
{/if}
{if $prefs.feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
<option value="tiki-user_bookmarks.php">{tr}Bookmarks{/tr}</option>
{/if}
{if $prefs.feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
<option value="tiki-newsreader_servers.php">{tr}Newsreader{/tr}</option>
{/if}
{if $prefs.user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
<option value="tiki-user_assigned_modules.php">{tr}Modules{/tr}</option>
{/if}
{if $prefs.feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
<option value="tiki-webmail.php">{tr}Webmail{/tr}</option>
{/if}
{if $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<option value="tiki-notepad_list.php">{tr}Notepad{/tr}</option>
{/if}
{if $prefs.feature_user_watches eq 'y'}
<option value="tiki-user_watches.php">{tr}Watches{/tr}</option>
{/if}
<option value="">----</option>
<option value="">{tr}MyTiki{/tr}</option>
{if $tiki_p_admin eq 'y' and $prefs.feature_debug_console eq 'y'}
<option value="">----</option>
<option value="javascript:toggle('debugconsole');">{tr}debug{/tr}</option>
{/if}
</select>
</form>
