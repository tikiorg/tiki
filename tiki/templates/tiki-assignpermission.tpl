<a href="tiki-assignpermission.php?group={$group}" class="pagetitle">{tr}Assign permissions to group{/tr}: {$group}</a><br/><br/>
<h3>{tr}Group Information{/tr}</h3>
<table class="normal">
<tr><td class="even">Name:</td><td class="odd">{$group_info.groupName}</td></tr>
<tr><td class="even">Desc:</td><td class="odd">{$group_info.groupDesc}</td></tr>
<tr><td class="even">Permissions:</td><td class="odd">
{section name=grp loop=$group_info.perms}
{$group_info.perms[grp]}{if $group_info.perms[grp] != "Anonymous"}(<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$group_info.perms[grp]}&amp;group={$group}&amp;action=remove">x</a>){/if}&nbsp;<br/>
{/section}
</td></tr>
</table>
</div>
<br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-assignpermission.php">
     <input type="text" name="find" value="{$find}" />
     <input type="hidden" name="group" value="{$group}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>




<table class="normal">
<tr>
  <td colspan="5" class="odd">
   [
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}">{tr}All{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=tiki">{tr}General{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=wiki">{tr}Wiki{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=file%20galleries">{tr}File gals{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=comments">{tr}Comments{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=blogs">{tr}Blogs{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=image%20galleries">{tr}Image gals{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=forums">{tr}Forums{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=comm">{tr}Comm{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=games">{tr}Games{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=quizzes">{tr}Quizzes{/tr}</a>
   ]<br/>
   [
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=cms">{tr}Cms{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=faqs">{tr}FAQs{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=user">{tr}user{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=chat">{tr}Chat{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=content%20templates">{tr}content templates{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=shoutbox">{tr}shoutbox{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=drawings">{tr}drawings{/tr}</a>
   ]
 </td>
</tr>
<tr>
<td class="heading"><a class="tableheading" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'permName_desc'}permName_asc{else}permName_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading">{tr}assigned{/tr}</td>
<td class="heading"><a class="tableheading" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}permDesc_asc{else}permDesc_desc{/if}">{tr}desc{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$perms}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$perms[user].permName}</td>
<td class="odd">{$perms[user].hasPerm}</td>
<td class="odd">{$perms[user].type}</td>
<td class="odd">{$perms[user].permDesc}</td>
<td class="odd">
{if $perms[user].hasPerm eq 'n'}
<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;perm={$perms[user].permName}&amp;group={$group}">{tr}assign{/tr}</a></td>
{else}
<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$perms[user].permName}&amp;group={$group}&amp;action=remove">remove</a></td>
{/if}
</tr>
{else}
<tr>
<td class="even">{$perms[user].permName}</td>
<td class="even">{$perms[user].hasPerm}</td>
<td class="even">{$perms[user].type}</td>
<td class="even">{$perms[user].permDesc}</td>
<td class="even">
{if $perms[user].hasPerm eq 'n'}
<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;perm={$perms[user].permName}&amp;group={$group}">{tr}assign{/tr}</a></td>
{else}
<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$perms[user].permName}&amp;group={$group}&amp;action=remove">remove</a></td>
{/if}
</tr>
{/if}
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-assignpermission.php?find={$find}&amp;type={$type}&amp;group={$group}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-assignpermission.php?find={$find}&amp;type={$type}&amp;group={$group}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
