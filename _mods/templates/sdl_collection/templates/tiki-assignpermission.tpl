{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-assignpermission.tpl,v 1.1 2004-05-09 23:09:15 damosoft Exp $ *}
{ *TODO: Must fix even/odd table rows detection byusing Smarty 'cycle' *}


<a href="tiki-assignpermission.php?group={$group}" class="pagetitle">{tr}Assign Permissions to Group{/tr}: {$group}</a>
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=PermissionAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Edit Article{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-assignpermission.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}edit article tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' /></a>
{/if}

<br/><br/>
<a href="tiki-admingroups.php" class="link">[{tr}Back to groups{/tr}]</a><br/>
<h3>{tr}Group Information{/tr}</h3>
<table class="normal">
<tr><td class="even">{tr}Name{/tr}:</td><td class="odd">{$group_info.groupName}</td><td>&nbsp;</td></tr>
<tr><td class="even">{tr}Description{/tr}:</td><td class="odd">{$group_info.groupDesc}</td><td>&nbsp;</td></tr>
<tr><td class="even">{tr}Permissions{/tr}:</td><td class="odd">
{section name=grp loop=$group_info.perms}
{$group_info.perms[grp]}{tr}&nbsp;{/tr}{if $group_info.perms[grp] != "Anonymous"}(<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$group_info.perms[grp]}&amp;group={$group}&amp;action=remove" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this permission?{/tr}')">Delete</a>){/if}<br />
{/section}
</td></tr>
</table>
<br />

<form method="post" action="tiki-assignpermission.php">
<input type="hidden" name="group" value="{$group|escape}" />
<input type="hidden" name="type" value="{$type|escape}" />
{tr}Create level{/tr}:<input type="text" name="level" /><input type="submit" name="createlevel" value="{tr}Go{/tr}" />
</form>
<br />
<br />
<form method="post" action="tiki-assignpermission.php">
<input type="hidden" name="group" value="{$group|escape}" />
<input type="hidden" name="type" value="{$type|escape}" />
<select name="oper">
<option value="assign">{tr}Assign{/tr}</option>
<option value="remove" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete all permissions in level?{/tr}')">{tr}Delete{/tr}</option>
</select>
{tr}all permissions in level{/tr}:
<select name="level">
{html_options output=$levels values=$levels selected=$perms[user].level}
</select>
<input type="submit" name="allper" value="{tr}Update{/tr}" />
</form>

<br /><br />
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="post" action="tiki-assignpermission.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="hidden" name="group" value="{$group|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>



<form name="tiki-assignpermission.php" method="post">
<input type="hidden" name="group" value="{$group|escape}" />
<input type="hidden" name="type" value="{$type|escape}" />
<input type="submit" name="update" value="{tr}Update{/tr}" />
<table class="normal">
<tr>
  <td colspan="7" class="odd">
   [
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=">{tr}All{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=tiki">{tr}General{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=wiki">{tr}Wiki{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=file%20galleries">{tr}File Galerys{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=comments">{tr}Comments{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=blogs">{tr}Blogs{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=image%20galleries">{tr}Image Galerys{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=forums">{tr}Forums{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=comm">{tr}Communication{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=games">{tr}Games{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=quizzes">{tr}Quizzes{/tr}</a>
   ]<br />
   [
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=cms">{tr}Articles{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=faqs">{tr}FAQs{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=user">{tr}User{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=chat">{tr}Chat{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=content%20templates">{tr}Content Templates{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=shoutbox">{tr}Shoutbox{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=drawings">{tr}Drawings{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=html%20pages">{tr}HTML Pages{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=trackers">{tr}Trackers{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=maps">{tr}Maps{/tr}</a>
   ]<br />
   [
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=surveys">{tr}Surveys{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=webmail">{tr}Webmail{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=newsletters">{tr}Newsletters{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=messu">{tr}Messages{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=dsn">{tr}DSN{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=directory">{tr}Directory{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=workflow">{tr}Workflow{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=charts">{tr}Charts{/tr}</a>|  
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=extwiki">{tr}External Wikis{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=support">{tr}Live Support{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=calendar">{tr}Calendar{/tr}</a>
   ]
 </td>
</tr>
<tr>
<td class="heading">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'permName_desc'}permName_asc{else}permName_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading">{tr}Level{/tr}</td>
<!--<td class="heading">{tr}Assgn{/tr}</td>-->
<td class="heading"><a class="tableheading" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}permDesc_asc{else}permDesc_desc{/if}">{tr}Description{/tr}</a></td>
<!-- <td class="heading">{tr}Action{/tr}</td> -->
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
<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;perm={$perms[user].permName}&amp;group={$group}">{tr}assign{/tr}</a></td>
{else}
<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$perms[user].permName}&amp;group={$group}&amp;action=remove" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this permission?{/tr}')">remove</a></td>
{/if}
-->
</tr>
{else}
<tr>
<td class="even"><input type="checkbox" name="perm[{$perms[user].permName}]" {if $perms[user].hasPerm eq 'y'}checked="checked"{/if}/></td>
<td class="even">{$perms[user].permName}</td>
<td class="even"><select name="level[{$perms[user].permName}]">{html_options output=$levels values=$levels selected=$perms[user].level}</select></td>
<!--<td class="even">{$perms[user].hasPerm}</td>-->
<td class="even">{$perms[user].type}</td>
<td class="even">{$perms[user].permDesc}</td>
<!--
<td class="even">
{if $perms[user].hasPerm eq 'n'}
<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;perm={$perms[user].permName}&amp;group={$group}">{tr}assign{/tr}</a></td>
{else}
<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$perms[user].permName}&amp;group={$group}&amp;action=remove" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this permisson?{/tr}')">remove</a></td>
{/if}
-->
</tr>
{/if}
{/section}
</table>
</form>
<br />
<div align="center">
<div class="mini">
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
</div>
