{* $Id$ *}

<h1><a href="tiki-assignuser.php{if $assign_user}?assign_user={$assign_user|escape:url}{/if}" class="pagetitle">{tr}Assign User {$assign_user} to Groups{/tr}</a></h1>

<div class="navbar">
{if $tiki_p_admin eq 'y'} {* only full admins can manage groups, not tiki_p_admin_users *}
<span class="button2"><a href="tiki-admingroups.php" class="linkbut">{tr}Admin groups{/tr}</a></span>
{/if}
{if $tiki_p_admin eq 'y' or $tiki_p_admin_users eq 'y'}
<span class="button2"><a href="tiki-adminusers.php" class="linkbut">{tr}Admin users{/tr}</a></span>
{/if}
</div>

{if $tiki_p_admin eq 'y' or $tiki_p_admin_users eq 'y'}
{if $prefs.feature_intertiki eq 'y' and !empty($prefs.feature_intertiki_mymaster)}
  <br /><b>{tr}Warning: since this tiki site is in slave mode, all user information you enter manually will be automatically overriden by other site's data, including users permissions{/tr}</b>
{/if}
{/if}
  
<h2>{tr}User Information{/tr}</h2>
<table class="normal">
<tr><td class="even">{tr}Login{/tr}:</td><td class="odd">{$user_info.login}</td></tr>
<tr><td class="even">{tr}Email{/tr}:</td><td class="odd">{$user_info.email}</td></tr>
<tr><td class="even">{tr}Groups{/tr}:</td><td class="odd">
{foreach from=$user_info.groups item=what key=grp}
{if $what eq 'included'}<i>{/if}{$grp}{if $what eq 'included'}</i>{/if}
{if $grp != "Anonymous" && $grp != "Registered"}
<a class="link" href="tiki-assignuser.php?{if $offset}offset={$offset}&amp;{/if}maxRecords={$prefs.maxRecords}&amp;sort_mode={$sort_mode}{if $assign_user}&amp;assign_user={$assign_user|escape:url}{/if}&amp;action=removegroup&amp;group={$grp|escape:url}" title="Remove">{icon _id='cross' alt='{tr}Remove{/tr}' style="vertical-align:middle"}</a>{if !user_info.groups.last},{/if}
{/if}&nbsp;&nbsp;
{/foreach}
</td></tr>
<form method="post" action="tiki-assignuser.php{if $assign_user}?assign_user={$assign_user}{/if}">
<tr><td class="even">{tr}Default Group{/tr}:</td><td class="odd">
<select name="defaultgroup">
<option value=""></option>
{foreach from=$user_info.groups key=name item=included}
<option value="{$name}" {if $name eq $user_info.default_group}selected="selected"{/if}>{$name}</option>
{/foreach}
</select>
<input type="hidden" value="{$user_info.login}" name="login" />
<input type="hidden" value="{$prefs.maxRecords}" name="maxRecords" />
<input type="hidden" value="{$offset}" name="offset" />
<input type="hidden" value="{$sort_mode}" name="sort_mode" />
<input type="submit" value="{tr}Set{/tr}" name="set_default" />
</form>
</td></tr>
</table>
<br />
<div align="left"><h2>{tr}Assign User {$assign_user} to Groups{/tr}</h2></div>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-assignuser.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     {if $assign_user}<input type="hidden" name="assign_user" value="{$assign_user|escape}" />{/if}
	 {tr}Number of displayed rows{/tr}</td><td  class="findtitle"><input type="text" name="maxRecords" value="{$prefs.maxRecords|escape}" size="3" />
   </form>
   </td>
</tr>
</table>

<table class="normal">
<tr>
<th class="heading"><a class="tableheading" href="tiki-assignuser.php?{if $assign_user}assign_user={$assign_user|escape:url}&amp;{/if}offset={$offset}&amp;maxRecords={$prefs.maxRecords}&amp;sort_mode={if $sort_mode eq 'groupName_desc'}groupName_asc{else}groupName_desc{/if}">{tr}Name{/tr}</a></th>
<th class="heading"><a class="tableheading" href="tiki-assignuser.php?{if $assign_user}assign_user={$assign_user|escape:url}&amp;{/if}offset={$offset}&amp;maxRecords={$prefs.maxRecords}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}groupDesc_asc{else}groupDesc_desc{/if}">{tr}Description{/tr}</a></th>
<th class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$users}
{if $users[user].groupName != 'Anonymous'}
<tr>
<td class="{cycle advance=false}">
{if $tiki_p_admin eq 'y'}<a class="link" href="tiki-assignpermission.php?group={$users[user].groupName|escape:url}" title="{tr}Assign Perms to this Group{/tr}">{icon _id='key' align="right" alt="{tr}Permissions{/tr}"}</a>{/if}{$users[user].groupName}</td>
<td class="{cycle advance=false}">{tr}{$users[user].groupDesc}{/tr}</td>
<td class="{cycle}">
{if $users[user].what ne 'real'}
<a class="link" href="tiki-assignuser.php?{if $offset}offset={$offset}&amp;{/if}maxRecords={$prefs.maxRecords}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;group={$users[user].groupName|escape:url}{if $assign_user}&amp;assign_user={$assign_user|escape:url}{/if}" title="{tr}Assign User to Group{/tr}">{icon _id='accept' alt='{tr}Assign{/tr}'}</a>
{elseif $users[user].groupName ne "Registered"}
<a class="link" href="tiki-assignuser.php?{if $offset}offset={$offset}&amp;{/if}maxRecords={$prefs.maxRecords}&amp;sort_mode={$sort_mode}{if $assign_user}&amp;assign_user={$assign_user|escape:url}{/if}&amp;action=removegroup&amp;group={$users[user].groupName|escape:url}" title="unassign">{icon _id='delete' alt='{tr}Unassign{/tr}'}</a>
{/if}
</td></tr>
{/if}
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-assignuser.php?find={$find}{if $assign_user}&amp;assign_user={$assign_user|escape:url}{/if}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;&nbsp;[<a class="prevnext" href="tiki-assignuser.php?find={$find}{if $assign_user}&amp;assign_user={$assign_user|escape:url}{/if}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-assignuser.php?find={$find}{if $assign_user}&amp;assign_user={$assign_user|escape:url}{/if}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
