<h2>{tr}Admin groups{/tr}</h2>
<div  align="center">
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>Find</td>
   <td>
   <form method="get" action="tiki-admingroups.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr>
<td class="heading"><a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupName_desc'}groupName_asc{else}groupName_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}groupDesc_asc{else}groupDesc_desc{/if}">{tr}desc{/tr}</a></td>
<td class="heading">{tr}Permissions{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$users}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$users[user].groupName}</td>
<td class="odd">{$users[user].groupDesc}</td>
<td class="odd">
{section name=grs loop=$users[user].perms}
{$users[user].perms[grs]}(<a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$users[user].perms[grs]}&amp;group={$users[user].groupName}&amp;action=remove">x</a>)&nbsp;
{/section}
</td>                                 
<td class="odd"><a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;group={$users[user].groupName}">{tr}delete{/tr}</a>
                                   <a class="link" href="tiki-assignpermission.php?group={$users[user].groupName}">assign_perms</a></td>
</tr>
{else}
<tr>
<td class="even">{$users[user].groupName}</td>
<td class="even">{$users[user].groupDesc}</td>
<td class="even">
{section name=grs loop=$users[user].perms}
{$users[user].perms[grs]}(<a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$users[user].perms[grs]}&amp;group={$users[user].groupName}&amp;action=remove">x</a>)&nbsp;
{/section}
</td>                                 

<td class="even"><a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;group={$users[user].groupName}">{tr}delete{/tr}</a>
                 <a class="link" href="tiki-assignpermission.php?group={$users[user].groupName}">assign_perms</a></td>

</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admingroups.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admingroups.php?offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>

</div>
<h3>{tr}Add a new group{/tr}</h3>
<form action="tiki-admingroups.php" method="post">
<table>
<tr><td>{tr}Group{/tr}:</td><td><input type="text" name="name" /></td></tr>
<tr><td>{tr}Desc{/tr}:</td><td><textarea rows="5" cols="20" name="desc"></textarea></td></tr>
<tr><td colspan="2"><input type="submit" name="newgroup" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
