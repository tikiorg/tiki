<a href="tiki-assignuser.php?assign_user={$assign_user}" class="pagetitle">{tr}Assign user{/tr} {$assign_user} {tr}to groups{/tr}</a><br/><br/>
<h3>User Information</h3>
<table class="normal">
<tr><td class="even">Login:</td><td class="odd">{$user_info.login}</td></tr>
<tr><td class="even">Email:</td><td class="odd">{$user_info.email}</td></tr>
<tr><td class="even">Groups:</td><td class="odd">
{section name=grp loop=$user_info.groups}
{$user_info.groups[grp]}{if $user_info.groups[grp] != "Anonymous"}(<a class="link" href="tiki-assignuser.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;assign_user={$assign_user}&amp;action=removegroup&amp;group={$user_info.groups[grp]}">x</a>){/if}&nbsp;&nbsp;
{/section}
</td></tr>
</table>
</div>
<br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">Find</td>
   <td class="findtable">
   <form method="get" action="tiki-assignuser.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-assignuser.php?assign_user={$assign_user}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupName_desc'}groupName_asc{else}groupName_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-assignuser.php?assign_user={$assign_user}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}groupDesc_asc{else}groupDesc_desc{/if}">{tr}desc{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$users}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$users[user].groupName}</td>
<td class="odd">{$users[user].groupDesc}</td>
<td class="odd">
{if $users[user].groupName != 'Anonymous'}
<a class="link" href="tiki-assignuser.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;group={$users[user].groupName}&amp;assign_user={$assign_user}">{tr}assign{/tr}</a></td>
{/if}
</tr>
{else}
<tr>
<td class="even">{$users[user].groupName}</td>
<td class="even">{$users[user].groupDesc}</td>
<td class="even">
{if $users[user].groupName != 'Anonymous'}
<a class="link" href="tiki-assignuser.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;group={$users[user].groupName}&amp;assign_user={$assign_user}">{tr}assign{/tr}</a>
{/if}
</td>
</tr>
{/if}
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-assignuser.php?assign_user={$assign_user}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;&nbsp;[<a class="prevnext" href="tiki-assignuser.php?assign_user={$assign_user}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>

