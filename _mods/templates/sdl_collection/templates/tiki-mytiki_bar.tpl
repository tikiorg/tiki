<div>
<table width="100%">
<tr>

<td valign="top"><a class="link" href="tiki-my_tiki.php" title="{tr}MyTiki{/tr}">
<img  border="0" src="img/mytiki/mytiki.gif" alt="{tr}My Area{/tr}" />
</a></td>

{if $feature_userPreferences eq 'y'}
<td valign="top"><a class="link" href="tiki-user_preferences.php" title="{tr}Preferences{/tr}">
<img  border="0" src="img/mytiki/prefs.gif" alt="{tr}Preferences{/tr}" />
</a></td>
{/if}

{if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<td valign="top"><a class="link" href="messu-mailbox.php" title="{tr}Messages{/tr}">
<img  border="0" src="img/mytiki/messages.gif" alt="{tr}Messages{/tr}" /><br />
<div><small>({$unread})</small></div>
</a></td>
{/if}

{if $feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
<td valign="top"><a class="link" href="tiki-user_tasks.php" title="{tr}Tasks{/tr}">
<img  border="0" src="img/mytiki/tasks.gif" alt="{tr}Tasks{/tr}" /><br />
</a></td>
{/if}


{if $feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
<td valign="top"><a class="link" href="tiki-user_bookmarks.php" title="{tr}Bookmarks{/tr}">
<img  border="0" src="img/mytiki/bookmarks.gif" alt="{tr}Bookmarks{/tr}" /><br />
</a></td>
{/if}



{if $user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
<td valign="top"><a class="link" href="tiki-user_assigned_modules.php" title="{tr}Modules{/tr}">
<img  border="0" src="img/mytiki/modules.gif" alt="{tr}Modules{/tr}" /><br />
</a></td>
{/if}


{if $feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
<td valign="top"><a class="link" href="tiki-newsreader_servers.php" title="{tr}Newsreader{/tr}">
<img  border="0" src="img/mytiki/news.gif" alt="{tr}Newsreader{/tr}" /><br />
</a></td>
{/if}

{if $feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
<td valign="top"><a class="link" href="tiki-webmail.php" title="{tr}Webmail{/tr}">
<img  border="0" src="img/mytiki/webmail.gif" alt="{tr}Webmail{/tr}" /><br />
</a></td>
{/if}

{if $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<td valign="top"><a class="link" href="tiki-notepad_list.php" title="{tr}Notepad{/tr}">
<img border="0" src="img/mytiki/notes.gif" alt="{tr}Notepad{/tr}" /><br />
</a></td>
{/if}

{if $feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
<td valign="top"><a class="link" href="tiki-userfiles.php" title="{tr}MyFiles{/tr}">
<img  border="0" src="img/mytiki/myfiles.gif" alt="{tr}My Files{/tr}" /><br />
</a></td>
{/if}

{if $feature_minical eq 'y'}
<td valign="top"><a class="link" href="tiki-minical.php" title="{tr}Mini Calendar{/tr}">
<img  border="0" src="img/mytiki/minical.gif" alt="{tr}Mini Calendar{/tr}" /><br />
</a></td>
{/if}

{if $feature_user_watches eq 'y'}
<td valign="top"><a class="link" href="tiki-user_watches.php" title="{tr}My watches{/tr}">
<img  border="0" src="img/mytiki/mywatches.gif" alt="{tr}My Watches{/tr}" /><br />
</a></td>
{/if}

</tr></table>
</div>

