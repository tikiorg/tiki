<div>
<table>
<tr>

<td valign="top"><a class="link" href="tiki-my_tiki.php">
<img width="48" height="48" border="0" src="img/mytiki/mytiki.gif" alt="{tr}MyTiki{/tr}" /><br/>
<div align="center"><small>{tr}MyTiki{/tr}</small></div>
</a></td>

<td valign="top"><a class="link" href="tiki-user_preferences.php">
<img  width="48" height="48" border="0" src="img/mytiki/prefs.gif" alt="{tr}Prefs{/tr}" /><br/>
<div align="center"><small>{tr}Prefs{/tr}</small></div>
</a></td>

{if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<td valign="top"><a class="link" href="messu-mailbox.php">
<img  width="48" height="48" border="0" src="img/mytiki/messages.gif" alt="{tr}Messages{/tr}" /><br/>
<div align="center"><small>{tr}Mesgs{/tr}<br/>({$unread})</small></div>
</a></td>
{/if}

{if $feature_tasks eq 'y'}
<td valign="top"><a class="link" href="tiki-user_tasks.php">
<img  width="48" height="48" border="0" src="img/mytiki/tasks.gif" alt="{tr}Tasks{/tr}" /><br/>
<div align="center"><small>{tr}Tasks{/tr}</small></div>
</a></td>
{/if}


{if $feature_user_bookmarks eq 'y'}
<td valign="top"><a class="link" href="tiki-user_bookmarks.php">
<img  width="48" height="48" border="0" src="img/mytiki/bookmarks.gif" alt="{tr}Bookmarks{/tr}" /><br/>
<div align="center"><small>{tr}Bookmarks{/tr}</small></div>
</a></td>
{/if}



{if $user_assigned_modules eq 'y'}
<td valign="top"><a class="link" href="tiki-user_assigned_modules.php">
<img  width="48" height="48" border="0" src="img/mytiki/modules.gif" alt="{tr}Modules{/tr}" /><br/>
<div align="center"><small>{tr}Modules{/tr}</small></div>
</a></td>
{/if}


{if $feature_newsreader eq 'y'}
<td valign="top"><a class="link" href="tiki-newsreader_servers.php">
<img  width="48" height="48" border="0" src="img/mytiki/news.gif" alt="{tr}Newsreader{/tr}" /><br/>
<div align="center"><small>{tr}Newsreader{/tr}</small></div>
</a></td>
{/if}

{if $feature_webmail eq 'y'}
<td valign="top"><a class="link" href="tiki-webmail.php">
<img  width="48" height="48" border="0" src="img/mytiki/webmail.gif" alt="{tr}Webmail{/tr}" /><br/>
<div align="center"><small>{tr}Webmail{/tr}</small></div>
</a></td>
{/if}

{if $feature_notepad eq 'y'}
<td valign="top"><a class="link" href="tiki-notepad_list.php">
<img  width="48" height="48" border="0" src="img/mytiki/notes.gif" alt="{tr}Notepad{/tr}" /><br/>
<div align="center"><small>{tr}Notepad{/tr}</small></div>
</a></td>
{/if}

{if $feature_userfiles eq 'y'}
<td valign="top"><a class="link" href="tiki-userfiles.php">
<img  width="48" height="48" border="0" src="img/mytiki/files.gif" alt="{tr}MyFiles{/tr}" /><br/>
<div align="center"><small>{tr}MyFiles{/tr}</small></div>
</a></td>
{/if}



</tr></table>
</div>

