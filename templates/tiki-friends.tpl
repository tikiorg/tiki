<a href="tiki-friends.php" class="pagetitle">{tr}Friendship Network{/tr}</a>
  
      {if $feature_help eq 'y'}
<a href="{$helpurl}FriendshipNetwork" target="tikihelp" class="tikihelp" title="{tr}Friendship Network{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}


      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-friends.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}friends tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt="{tr}edit tpl{/tr}" /></a>{/if}

<br /><br />


{if $tiki_p_create_users eq 'y'}
<a class="userlink" href="tiki-edit_user.php">{tr}create new user{/tr}</a>
{/if}
<br/><br/>

{if $request_friendship}
<center>{tr}FriendshipRequestSent{/tr}</center>
<br/><br/>
{/if}
{if $friendship_accepted}
<center>{$friendship_accepted}</center>
<br/><br/>
{/if}
{if $friendship_refused}
<center>{$friendship_refused}</center>
<br/><br/>
{/if}

<div align="center">
{if $pending_requests|sizeof}
{tr}Pending requests{/tr}
<table class="userlist">
<tr>
  <td class="userlistheading">{tr}Login{/tr}</td>
  <td class="userlistheading">{tr}Request Time{/tr}</td>
  <td class="userlistheading">{tr}Actions{/tr}</td>
</tr>

{foreach from=$pending_requests item=time key=pending_user}
<tr>
  <td class="userlistlogin">{$pending_user|userlink}</td>
  <td class="userlistlogin">{$time|date_format}</td>
  <td class="userlistlogin"><a href="tiki-friends.php?accept={$pending_user}">{tr}accept{/tr}</a>|<a href="tiki-friends.php?refuse={$pending_user}">{tr}refuse{/tr}</a></td>
</tr>
{/foreach}
</table>
{/if}

{if $waiting_requests|sizeof}
{tr}Waiting requests{/tr}
<table class="userlist">
<tr>
  <td class="userlistheading">{tr}Login{/tr}</td>
  <td class="userlistheading">{tr}Request Time{/tr}</td>
</tr>

{foreach from=$waiting_requests item=time key=wuser}
<tr>
  <td class="userlistlogin">{$wuser|userlink}</td>
  <td class="userlistlogin">{$time|date_format}</td>
</tr>
{/foreach}
</table>
{/if}

<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-friends.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="userlist">
{if $listpages}
<tr>
  <td class="userlistheading"><a class="userlistheading" href="tiki-friends.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}">{tr}Login{/tr}</a></td>
  <td class="userlistheading"><a class="userlistheading" href="tiki-friends.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'realName_desc'}realName_asc{else}realName_desc{/if}">{tr}Real Name{/tr}</a></td>
</tr>
{/if}
{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
<tr>
  <td class="userlistlogin{cycle advance=false}">
  {if $feature_score eq 'y'}{$listpages[channel].score|star}{/if}<a class="userlistlogin{cycle advance=false}" href="tiki-user_information.php?view_user={$listpages[changes].login}">{$listpages[changes].login}</a>
  </td>
  <td class="userlistrealname{cycle advance=false}"><a class="userlistlogin{cycle advance=false}" href="tiki-user_information.php?view_user={$listpages[changes].login}">{$listpages[changes].realname}</a></td>
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="userprevnext" href="tiki-friends.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="userprevnext" href="tiki-friends.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-friends.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
