<span style="position:absolute;left:195px;top:0;" id="tiki-top">

<table callpadding="0" cellspacing="0" border="0">
<tr>

<td valign="top"><a class="link" href="tiki-my_tiki.php" title="{tr}MyTiki{/tr}">
{tr}MyTiki{/tr}
</a></td>

{if $feature_userPreferences eq 'y'}
<td valign="top"><a class="link" href="tiki-user_preferences.php" title="{tr}Preferences{/tr}">
{tr}Prefs{/tr}
</a></td>
{/if}

{if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<td valign="top"><a class="link" href="messu-mailbox.php" title="{tr}Messages{/tr}">
{tr}Messages{/tr} <small>({$unread})</small>
</a></td>
{/if}

{if $feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
<td valign="top"><a class="link" href="tiki-user_tasks.php" title="{tr}Tasks{/tr}">
{tr}Tasks{/tr}
</a></td>
{/if}

{if $feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
<td valign="top"><a class="link" href="tiki-user_bookmarks.php" title="{tr}Bookmarks{/tr}">
{tr}Bookmarks{/tr}
</a></td>
{/if}

{if $user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
<td valign="top"><a class="link" href="tiki-user_assigned_modules.php" title="{tr}Modules{/tr}">
{tr}Modules{/tr}
</a></td>
{/if}

{if $feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
<td valign="top"><a class="link" href="tiki-newsreader_servers.php" title="{tr}Newsreader{/tr}">
{tr}Newsreader{/tr}
</a></td>
{/if}

{if $feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
<td valign="top"><a class="link" href="tiki-webmail.php" title="{tr}Webmail{/tr}">
{tr}Webmail{/tr}
</a></td>
{/if}

{if $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<td valign="top"><a class="link" href="tiki-notepad_list.php" title="{tr}Notepad{/tr}">
{tr}Notepad{/tr}
</a></td>
{/if}

{if $feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
<td valign="top"><a class="link" href="tiki-userfiles.php" title="{tr}MyFiles{/tr}">
{tr}MyFiles{/tr}
</a></td>
{/if}


{if $feature_minical eq 'y'}
<td valign="top"><a class="link" href="tiki-minical.php" title="{tr}Calendar{/tr}">
{tr}Calendar{/tr}
</a></td>
{/if}


</tr></table>
</span>

