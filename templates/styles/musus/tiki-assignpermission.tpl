{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-assignpermission.tpl,v 1.2 2004-01-13 19:33:01 musus Exp $ *}
{ *TODO: Must fix even/odd table rows detection byusing Smarty 'cycle' *}

<a href="tiki-assignpermission.php?group={$group}" class="pagetitle">{tr}Assign permissions to group{/tr}: {$group}</a>
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=PermissionAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Edit Article{/tr}"><img src='img/icons/help.gif' alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-assignpermission.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}edit article tpl{/tr}"><img src='img/icons/info.gif' alt='{tr}edit template{/tr}' /></a>
{/if}

<h3>{tr}Group Information{/tr}</h3>
<table>
<tr><td>{tr}Name{/tr}:</td><td>{$group_info.groupName}</td></tr>
<tr><td>{tr}Desc{/tr}:</td><td>{$group_info.groupDesc}</td></tr>
<tr><td>{tr}Permissions{/tr}:</td><td>
{section name=grp loop=$group_info.perms}
{$group_info.perms[grp]}{if $group_info.perms[grp] != "Anonymous"}(<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$group_info.perms[grp]}&amp;group={$group}&amp;action=remove">x</a>){/if}&nbsp;<br />
{/section}
</td></tr>
</table>
<br />

<form method="post" action="tiki-assignpermission.php">
<input type="hidden" name="group" value="{$group|escape}" />
<input type="hidden" name="type" value="{$type|escape}" />
{tr}Create level{/tr}:<input type="text" name="level" /><input type="submit" name="createlevel" value="{tr}create{/tr}" />
</form>
<br />
<br />
<form method="post" action="tiki-assignpermission.php">
<input type="hidden" name="group" value="{$group|escape}" />
<input type="hidden" name="type" value="{$type|escape}" />
<select name="oper">
<option value="assign">{tr}assign{/tr}</option>
<option value="remove">{tr}remove{/tr}</option>
</select>
{tr}all permissions in level{/tr}:
<select name="level">
{html_options output=$levels values=$levels selected=$perms[user].level}
</select>
<input type="submit" name="allper" value="{tr}update{/tr}" />
</form>

<br /><br />
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="post" action="tiki-assignpermission.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="hidden" name="group" value="{$group|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<form name="tiki-assignpermission.php" method="post">
<input type="hidden" name="group" value="{$group|escape}" />
<input type="hidden" name="type" value="{$type|escape}" />
<input type="submit" name="update" value="{tr}update{/tr}" />
<table>
<tr>
  <td colspan="7" class="odd">
   [
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=">{tr}All{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=tiki">{tr}General{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=wiki">{tr}Wiki{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=file%20galleries">{tr}File gals{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=comments">{tr}Comments{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=blogs">{tr}Blogs{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=image%20galleries">{tr}Image gals{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=forums">{tr}Forums{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=comm">{tr}Comm{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=games">{tr}Games{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=quizzes">{tr}Quizzes{/tr}</a>
   ]<br />
   [
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=cms">{tr}Cms{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=faqs">{tr}FAQs{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=user">{tr}User{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=chat">{tr}Chat{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=content%20templates">{tr}Content Templates{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=shoutbox">{tr}Shoutbox{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=drawings">{tr}Drawings{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=html%20pages">{tr}HTML pages{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=trackers">{tr}Trackers{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=maps">{tr}Maps{/tr}</a>
   ]<br />
   [
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=surveys">{tr}Surveys{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=webmail">{tr}Webmail{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=newsletters">{tr}Newsletters{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=messu">{tr}Messages{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=dsn">{tr}DSN{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=directory">{tr}Directory{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=workflow">{tr}Workflow{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=charts">{tr}Charts{/tr}</a>|  
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=extwiki">{tr}ExtWikis{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=support">{tr}Live support{/tr}</a>|
   <a href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=calendar">{tr}Calendar{/tr}</a>
   ]
 </td>
</tr>
<tr>
<td class="heading">&nbsp;</td>
<td class="heading"><a href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'permName_desc'}permName_asc{else}permName_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading">{tr}level{/tr}</td>
<!--<td class="heading">{tr}assgn{/tr}</td>-->
<td class="heading"><a href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></td>
<td class="heading"><a href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}permDesc_asc{else}permDesc_desc{/if}">{tr}desc{/tr}</a></td>
<!-- <td class="heading">{tr}action{/tr}</td> -->
</tr>
{section name=user loop=$perms}
<input type="hidden" name="permName[{$perms[user].permName}]" />
{if $smarty.section.user.index % 2}
<tr>
<td class="odd"><input type="checkbox" name="perm[{$perms[user].permName}]" {if $perms[user].hasPerm eq 'y'}checked="checked"{/if}/></td>
<td class="odd">{$perms[user].permName}</td>
<td class="odd"><select name="level[{$perms[user].permName}]">{html_options output=$levels values=$levels selected=$perms[user].level}</select></td>
<!--<td class="odd">{$perms[user].hasPerm}</td>-->
<td class="odd">{tr}{$perms[user].type}{/tr}</td>
<td class="odd">{tr}{$perms[user].permDesc}{/tr}</td>
<!--
<td class="odd">
{if $perms[user].hasPerm eq 'n'}
<a href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;perm={$perms[user].permName}&amp;group={$group}">{tr}assign{/tr}</a></td>
{else}
<a href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$perms[user].permName}&amp;group={$group}&amp;action=remove">remove</a></td>
{/if}
-->
</tr>
{else}
<tr class="even">
<td><input type="checkbox" name="perm[{$perms[user].permName}]" {if $perms[user].hasPerm eq 'y'}checked="checked"{/if}/></td>
<td>{$perms[user].permName}</td>
<td><select name="level[{$perms[user].permName}]">{html_options output=$levels values=$levels selected=$perms[user].level}</select></td>
<!--<td>{$perms[user].hasPerm}</td>-->
<td>{$perms[user].type}</td>
<td>{$perms[user].permDesc}</td>
<!--
<td>
{if $perms[user].hasPerm eq 'n'}
<a href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;perm={$perms[user].permName}&amp;group={$group}">{tr}assign{/tr}</a></td>
{else}
<a href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$perms[user].permName}&amp;group={$group}&amp;action=remove">remove</a></td>
{/if}
-->
</tr>
{/if}
{/section}
</table>
</form>
<br />
<div align="center" class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-assignpermission.php?find={$find}&amp;type={$type}&amp;group={$group}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-assignpermission.php?find={$find}&amp;type={$type}&amp;group={$group}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-assignpermission.php?find={$find}&amp;type={$type}&amp;group={$group}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>