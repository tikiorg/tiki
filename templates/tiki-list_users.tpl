<span class="pagetitle">{tr}Member list{/tr}</span>

      {if $feature_help eq 'y'}
<a href="{$helpurl}MemberList" target="tikihelp" class="tikihelp" title="{tr}Member List{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<br/><br/>
{if $tiki_p_create_users eq 'y'}
<a class="userlink" href="tiki-edit_user.php">{tr}create new user{/tr}</a>
{/if} 
<br/><br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_users.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="userlist">
<tr>
  <td class="userlistheading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}">{tr}Login{/tr}</a></td>
  <td class="userlistheading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'realName_desc'}realName_asc{else}realName_desc{/if}">{tr}Real Name{/tr}</a></td>
{if $feature_score eq 'y'}
  <td class="userlistheading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'score_desc'}score_asc{else}score_desc{/if}">{tr}Score{/tr}</a></td>
{/if}
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listusers}
<tr>
  <td class="userlistlogin{cycle advance=false}">&nbsp;{$listusers[changes].login|userlink}&nbsp;</td>
  <td class="userlistlogin{cycle advance=false}">&nbsp;{$listusers[changes].realName}&nbsp;</td>
{if $feature_score eq 'y'}
  <td class="userlistlogin{cycle advance=false}">&nbsp;{$listusers[changes].score}&nbsp;</td>
{/if}
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
[<a class="userprevnext" href="tiki-list_users.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="userprevnext" href="tiki-list_users.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_users.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
