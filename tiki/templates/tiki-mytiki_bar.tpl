<div>
<table>
<tr>
<td><div class="button2"><a class="linkbut" href="tiki-my_tiki.php">{tr}MyTiki{/tr}</a></div></td>
<td><div class="button2"><a class="linkbut" href="tiki-user_preferences.php">{tr}Prefs{/tr}</a></div></td>
{if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<td><div class="button2"><a class="linkbut" href="messu-mailbox.php">{tr}Messages{/tr} ({$unread})</div></td>
{/if}
{if $feature_tasks eq 'y'}
<td><div class="button2"><a class="linkbut" href="tiki-user_tasks.php">{tr}Tasks{/tr}</div></td>
{/if}
{if $feature_user_bookmarks eq 'y'}
<td><div class="button2"><a class="linkbut" href="tiki-user_bookmarks.php">{tr}Bookmarks{/tr}</div></td>
{/if}
{if $user_assigned_modules eq 'y'}
<td><div class="button2"><a class="linkbut" href="tiki-user_assigned_modules.php">{tr}Modules{/tr}</div></td>
{/if}
</tr></table>
</div>

