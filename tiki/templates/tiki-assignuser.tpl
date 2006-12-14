{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-assignuser.tpl,v 1.30 2006-12-14 16:40:32 sylvieg Exp $ *}

<h1><a href="tiki-assignuser.php?assign_user={$assign_user|escape:url}" class="pagetitle">{tr}Assign user{/tr} {$assign_user} {tr}to groups{/tr}</a></h1>
<a href="tiki-adminusers.php" class="linkbut">{tr}Admin users{/tr}</a> <a href="tiki-admingroups.php" class="linkbut">{tr}Admin groups{/tr}</a>
<br />

{if $feature_intertiki eq 'y' and !empty($feature_intertiki_mymaster)}
  <br /><b>{tr}Warning: since this tiki site is in slave mode, all user information you enter manually will be automatically overriden by other site's data, including users permissions{/tr}</b>
{/if}
  
<h2>{tr}User Information{/tr}</h2>
<table class="normal">
<tr><td class="even">{tr}Login{/tr}:</td><td class="odd">{$user_info.login}</td></tr>
<tr><td class="even">{tr}Email{/tr}:</td><td class="odd">{$user_info.email}</td></tr>
<tr><td class="even">{tr}Groups{/tr}:</td><td class="odd">
{foreach from=$user_info.groups item=what key=grp}
{if $what eq 'included'}<i>{/if}{$grp}{if $what eq 'included'}</i>{/if}
{if $grp != "Anonymous"}
(<a class="link" href="tiki-assignuser.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;assign_user={$assign_user|escape:url}&amp;action=removegroup&amp;group={$grp|escape:url}" title="remove">x</a>)
{/if}&nbsp;&nbsp;
{/foreach}
</td></tr>
<form method="post" action="tiki-assignuser.php?assign_user={$assign_user}">
<tr><td class="even">{tr}Default Group{/tr}:</td><td class="odd">
<select name="defaultgroup">
<option value=""></option>
{foreach from=$user_info.groups key=name item=included}
<option value="{$name}" {if $name eq $user_info.default_group}selected="selected"{/if}>{$name}</option>
{/foreach}
</select>
<input type="hidden" value="{$user_info.login}" name="login" />
<input type="submit" value="{tr}set{/tr}" name="set_default" />
</form>
</td></tr>
</table>

<br />
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-assignuser.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="assign_user" value="{$assign_user|escape}" />
   </form>
   </td>
</tr>
</table>

<div align="left"><h2>{tr}Available groups{/tr}</h2></div>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-assignuser.php?assign_user={$assign_user|escape:url}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupName_desc'}groupName_asc{else}groupName_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-assignuser.php?assign_user={$assign_user|escape:url}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}groupDesc_asc{else}groupDesc_desc{/if}">{tr}desc{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$users}
<tr>
<td class="{cycle advance=false}">{$users[user].groupName}
{if $tiki_p_admin eq 'y'}(<a class="link" href="tiki-assignpermission.php?group={$users[user].groupName|escape:url}">{tr}assign perms to this group{/tr}</a>){/if}</td>
<td class="{cycle advance=false}">{tr}{$users[user].groupDesc}{/tr}</td>
<td class="{cycle}">
{if $users[user].groupName != 'Anonymous'}
<a class="link" href="tiki-assignuser.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;group={$users[user].groupName|escape:url}&amp;assign_user={$assign_user|escape:url}">{tr}assign{/tr} {$user_info.login} {tr}to{/tr} "{$users[user].groupName}"</a></td>
{/if}
</tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-assignuser.php?find={$find}&amp;assign_user={$assign_user|escape:url}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;&nbsp;[<a class="prevnext" href="tiki-assignuser.php?find={$find}&amp;assign_user={$assign_user|escape:url}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-assignuser.php?find={$find}&amp;assign_user={$assign_user|escape:url}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>
