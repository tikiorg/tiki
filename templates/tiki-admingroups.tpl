{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admingroups.tpl,v 1.11 2003-08-01 10:31:09 redflo Exp $ *}

<a class="pagetitle" href="tiki-admingroups.php">{tr}Admin groups{/tr}</a><br /><br />
{if $groupname eq ''}
<h3>{tr}Add New Group{/tr}</h3>
{else}
<h3>{tr}Edit this group:{/tr} {$groupname}</h3>
<a href="tiki-admingroups.php">Add new group</a>
{/if}
<form action="tiki-admingroups.php" method="post">
<table class="normal">
<tr><td class="formcolor">{tr}Group{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$groupname|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Desc{/tr}:</td><td class="formcolor"><textarea rows="5" cols="20" name="desc">{$groupdesc|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Include{/tr}:</td><td class="formcolor">
<select name="include_groups[]" multiple="multiple" size="4">
{section name=ix loop=$users}
{assign var="inced" value="$users[ix].groupName"}
<option value="{$inced|escape}" {if $inc.$inced eq 'y'} selected="selected"{/if}>{$inced}</option>
{/section}
</select>
</td></tr>
{if $group ne ''}
<tr><td  class="formcolor">&nbsp;
<input type="hidden" name="olgroup" value="{$group|escape}">
</td><td  class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
{else}
<tr><td  class="formcolor">&nbsp;</td><td  class="formcolor"><input type="submit" name="newgroup" value="{tr}Add{/tr}" /></td></tr>
{/if}
</table>
</form>
<br /><br />
<h3>{tr}List of existing groups{/tr}</h3>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admingroups.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
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
{cycle values="even,odd" print=false}
{section name=user loop=$users}
<tr>
<td class="{cycle advance=false}">{$users[user].groupName}</td>
<td class="{cycle advance=false}">{$users[user].groupDesc}</td>
<td class="{cycle advance=false}">
{section name=ix loop=$users[user].included}
{$users[user].included[ix]}<br />
{/section}
</td>
<td class="{cycle advance=false}">
{section name=grs loop=$users[user].perms}
{$users[user].perms[grs]}(<a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$users[user].perms[grs]}&amp;group={$users[user].groupName}&amp;action=remove">x</a>)<br />
{/section}
</td>                                 
<td class="{cycle}">
{if $users[user].groupName !== 'Anonymous'}
<a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;group={$users[user].groupName}">{tr}delete{/tr}</a>
{/if}
<a class="link" href="tiki-assignpermission.php?group={$users[user].groupName}">{tr}assign_perms{/tr}</a>
<a class="link" href="tiki-admingroups.php?group={$users[user].groupName}">{tr}edit{/tr}</a></td>
</tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admingroups.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admingroups.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admingroups.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>

</div>
