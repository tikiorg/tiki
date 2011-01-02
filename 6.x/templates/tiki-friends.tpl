{title help="Friendship+Network"}{tr}Friendship Network{/tr}{/title}

{if $msg}
  <center>{$msg}</center>
  <br /><br />
{/if}

{if $pending_requests|@count}
<p style="font-weight: bold;">{tr}Pending requests{/tr}</p>
<table class="userlist">
<tr>
  <td class="userlistheading">{tr}Login{/tr}</td>
  <td class="userlistheading">{tr}Request Time{/tr}</td>
  <td class="userlistheading">{tr}Actions{/tr}</td>
</tr>

{foreach from=$pending_requests item=time key=pending_user}
<tr>
  <td class="userlistodd">{$pending_user|userlink}</td>
  <td class="userlistodd">{$time|date_format}</td>
  <td class="userlistodd"><a href="tiki-friends.php?accept={$pending_user}">{tr}Accept{/tr}</a>|<a href="tiki-friends.php?refuse={$pending_user}">{tr}Refuse{/tr}</a></td>
</tr>
{/foreach}
</table>
{/if}

{if $waiting_requests|@count}
<p style="font-weight: bold;">{tr}Waiting requests{/tr}</p>
<table class="userlist">
<tr>
  <td class="userlistheading">{tr}Login{/tr}</td>
  <td class="userlistheading">{tr}Request Time{/tr}</td>
  <td class="userlistheading">{tr}Actions{/tr}</td>
</tr>

{foreach from=$waiting_requests item=time key=wuser}
<tr>
  <td class="userlistodd">{$wuser|userlink}</td>
  <td class="userlistodd">{$time|date_format}</td>
  <td class="userlistodd"><a href="tiki-friends.php?cancel_waiting_friendship={$wuser}">{tr}Cancel friendship request{/tr}</a></td>
</tr>
{/foreach}
</table>
{/if}
<p style="font-weight: bold;">{tr}Your friends{/tr}</p>

{include file='find.tpl'}
{jq}$("#find").tiki("autocomplete", "username"){/jq}

<table class="userlist">
{if $listpages}
<tr>
  <td class="userlistheading"><a class="userlistheading" href="tiki-friends.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}">{tr}Login{/tr}</a></td>
  <td class="userlistheading"><a class="userlistheading" href="tiki-friends.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pref:realName_desc'}pref:realName_asc{else}pref:realName_desc{/if}">{tr}Real Name{/tr}</a></td>
  <td class="userlistheading">{tr}Action{/tr}</td>
</tr>
{/if}
{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
<tr>
  <td class="userlist{cycle advance=false}">
  <a class="userlistlogin{cycle advance=false}" href="tiki-user_information.php?view_user={$listpages[changes].login}">{$listpages[changes].login|userlink}</a>{if $prefs.feature_score eq 'y'}{$listpages[changes].score|star}{/if}
  </td>
  <td class="userlist{cycle advance=false}"><a class="userlistlogin{cycle advance=false}" href="tiki-user_information.php?view_user={$listpages[changes].login}">{$listpages[changes].realname}</a></td>
  <td class="userlist{cycle advance=true}"><a class="userlistlogin{cycle advance=false}" href="?break={$listpages[changes].login}">{icon _id='cross' alt="{tr}break friendship{/tr}"}</a></td>
</tr>
{sectionelse}
<tr><td colspan="6" class="odd">
{tr}No records found{/tr}
</td></tr>
{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset }{/pagination_links}
