<a class="pagetitle" href="tiki-admingroups.php">{tr}Admin groups{/tr}</a><br/><br/>
<h3>{tr}Add a new group{/tr}</h3>
<form action="tiki-admingroups.php" method="post">
<table class="normal">
<tr><td class="formcolor">{tr}Group{/tr}:</td><td class="formcolor"><input type="text" name="name" /></td></tr>
<tr><td class="formcolor">{tr}Desc{/tr}:</td><td class="formcolor"><textarea rows="5" cols="20" name="desc"></textarea></td></tr>
<tr><td class="formcolor">{tr}Include{/tr}:</td><td class="formcolor">
<select name="include_groups[]" multiple="multiple" size="4">
{section name=ix loop=$users}
<option value="{$users[ix].groupName}">{$users[ix].groupName}</option>
{/section}
</select>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td  class="formcolor"><input type="submit" name="newgroup" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<br/><br/>
<h3>{tr}List of existing groups{/tr}</h3>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admingroups.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupName_desc'}groupName_asc{else}groupName_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}groupDesc_asc{else}groupDesc_desc{/if}">{tr}desc{/tr}</a></td>
<td class="heading">{tr}Includes{/tr}</td>
<td class="heading">{tr}Permissions{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$users}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$users[user].groupName}</td>
<td class="odd">{$users[user].groupDesc}</td>
<td class="odd">
{section name=ix loop=$users[user].included}
{$users[user].included[ix]}<br/>
{/section}
</td>
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
{section name=ix loop=$users[user].included}
{$users[user].included[ix]}<br/>
{/section}
</td>

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
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admingroups.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admingroups.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admingroups.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>

</div>
