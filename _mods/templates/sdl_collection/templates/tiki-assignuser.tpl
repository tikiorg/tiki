{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-assignuser.tpl,v 1.1 2004-05-09 23:09:15 damosoft Exp $ *}

<a href="tiki-assignuser.php?assign_user={$assign_user}" class="pagetitle">{tr}Assign User{/tr} {$assign_user} {tr}to Groups{/tr}</a><br/><br/>
<h3>{tr}User Information{/tr}</h3>
<table class="normal">
<tr><td class="even">{tr}Login{/tr}:</td><td class="odd">{$user_info.login}</td></tr>
<tr><td class="even">{tr}Email{/tr}:</td><td class="odd">{$user_info.email}</td></tr>
<tr><td class="even">{tr}Groups{/tr}:</td><td class="odd">
{foreach from=$user_info.groups item=grp}
{$grp}
{if $grp != "Anonymous"}
(<a class="link" href="tiki-assignuser.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;assign_user={$assign_user}&amp;action=removegroup&amp;group={$grp}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this group?{/tr}')">Delete</a>)
{/if}&nbsp;&nbsp;
{/foreach}
</td></tr>
<form method="post" action="tiki-assignuser.php?assign_user={$assign_user}">
<tr><td class="even">{tr}Default Group{/tr}:</td><td class="odd">
<select name="defaultgroup">
{foreach from=$user_info.groups item=grp}
<option value="{$grp}" {if $grp eq $user_info.default_group}selected="selected"{/if}>{$grp}</option>
{/foreach}
</select>
<input type="hidden" value="{$user_info.login}" name="login">
<input type="submit" value="{tr}Set{/tr}" name="set_default" />
</form>
</td></tr>
</table>

<br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-assignuser.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
</div>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-assignuser.php?assign_user={$assign_user}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupName_desc'}groupName_asc{else}groupName_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-assignuser.php?assign_user={$assign_user}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}groupDesc_asc{else}groupDesc_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$users}
<tr>
<td class="{cycle advance=false}">{$users[user].groupName}</td>
<td class="{cycle advance=false}">{$users[user].groupDesc}</td>
<td class="{cycle}">
{if $users[user].groupName != 'Anonymous'}
<a class="link" href="tiki-assignuser.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;group={$users[user].groupName}&amp;assign_user={$assign_user}">{tr}assign{/tr}</a></td>
{/if}
</tr>
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-assignuser.php?find={$find}&amp;assign_user={$assign_user}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;&nbsp;[<a class="prevnext" href="tiki-assignuser.php?find={$find}&amp;assign_user={$assign_user}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-assignuser.php?find={$find}&amp;assign_user={$assign_user}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>

