~np~ {* ~np~ is needed since userlink smarty modifier includes '[' and ']' in date formats, which are parsed as links in wiki syntax. *}
<table class="userlist">
<tr>
  <td class="userlistheading"><a class="userlistheading" href="tiki-list_users.php?offset=0&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}">{tr}Login{/tr}</a></td>
{if $prefs.user_show_realnames neq 'y'}
  <td class="userlistheading"><a class="userlistheading" href="tiki-list_users.php?offset=0&amp;sort_mode={if $sort_mode eq 'pref:realName_desc'}pref:realName_asc{else}pref:realName_desc{/if}">{tr}Real Name{/tr}</a></td>
{/if}
{if $prefs.feature_score eq 'y'}
  <td class="userlistheading"><a class="userlistheading" href="tiki-list_users.php?offset=0&amp;sort_mode={if $sort_mode eq 'score_desc'}score_asc{else}score_desc{/if}">{tr}Score{/tr}</a></td>
{/if}
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listusers}
<tr>
  <td class="userlist{cycle advance=false}">&nbsp;{$listusers[changes].login|userlink}&nbsp;</td>
{if $prefs.user_show_realnames neq 'y'}
  <td class="userlist{cycle advance=false}">&nbsp;{$listusers[changes].login|username}&nbsp;</td>
{/if}
{if $prefs.feature_score eq 'y'}
  <td class="userlist{cycle advance=true}">&nbsp;{$listusers[changes].score}&nbsp;</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
~/np~
