{if $mytikivis eq 'show'}
<a class="link" href="tiki-my_tiki.php" title="{tr}MyTiki{/tr}">{tr}MyTiki{/tr}</a>

{if $feature_userPreferences eq 'y'}
<a class="link" href="tiki-user_preferences.php" title="{tr}Preferences{/tr}">{tr}Prefs{/tr}</a>{/if}

{if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<a class="link" href="messu-mailbox.php" title="{tr}Messages{/tr}">{tr}Messages{/tr}{if $unread} <small>({$unread})</small>{/if}</a>{/if}

{if $feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
<a class="link" href="tiki-user_tasks.php" title="{tr}Tasks{/tr}">{tr}Tasks{/tr}</a>{/if}

{if $feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
<a class="link" href="tiki-user_bookmarks.php" title="{tr}Bookmarks{/tr}">{tr}Bookmarks{/tr}</a>{/if}

{if $user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
<a class="link" href="tiki-user_assigned_modules.php" title="{tr}Modules{/tr}">{tr}Modules{/tr}</a>{/if}

{if $feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
<a class="link" href="tiki-newsreader_servers.php" title="{tr}Newsreader{/tr}">{tr}Newsreader{/tr}</a>{/if}

{if $feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
<a class="link" href="tiki-webmail.php" title="{tr}Webmail{/tr}">{tr}Webmail{/tr}</a>{/if}

{if $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a class="link" href="tiki-notepad_list.php" title="{tr}Notepad{/tr}">{tr}Notepad{/tr}</a>{/if}

{if $feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
<a class="link" href="tiki-userfiles.php" title="{tr}MyFiles{/tr}">{tr}MyFiles{/tr}</a>{/if}

{if $feature_minical eq 'y'}
<a class="link" href="tiki-minical.php" title="{tr}Calendar{/tr}">{tr}Calendar{/tr}</a>{/if}
{/if}
