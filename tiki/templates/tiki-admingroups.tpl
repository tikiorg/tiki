{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admingroups.tpl,v 1.31 2004-01-21 07:07:23 mose Exp $ *}
{popup_init src="lib/overlib.js"}

<a class="pagetitle" href="tiki-admingroups.php">{tr}Admin groups{/tr}</a>
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=PermissionAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin groups{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' />{/if}
{if $feature_help eq 'y'}</a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admingroups.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin groups tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' />{/if}
{if $feature_view_tpl eq 'y'}</a>{/if}
<br />

<span class="button2"><a href="tiki-admingroups.php" class="linkbut">{tr}Admin groups{/tr}</a></span>
<span class="button2"><a href="tiki-adminusers.php" class="linkbut">{tr}Admin users{/tr}</a></span>
<br /><br /><br />

{cycle name=tabs values="1,2,3,4" print=false advance=false}
<div class="tabs">
<span id="tab{cycle name=tabs}" class="tab tabActive">{tr}List{/tr}</span>
<span id="tab{cycle name=tabs}" class="tab">{tr}Add/Edit{/tr}</span>
{if $ins_fields}
<span id="tab{cycle name=tabs}" class="tab">{tr}More info{/tr}</span>
{/if}
{if $memberslist}
<span id="tab{cycle name=tabs}" class="tab">{tr}Members{/tr}</span>
{/if}
</div>

{cycle name=content values="1,2,3,4" print=false advance=false}

{* ----------------------- tab with list --------------------------------------- *}
<div id="content{cycle name=content}" class="content">
<h3>{tr}List of existing groups{/tr}</h3>

<form method="get" action="tiki-admingroups.php">
<table class="findtable"><tr>
<td><label for="groups_find">{tr}Find{/tr}</label></td>
<td><input type="text" name="find" id="groups_find" value="{$find|escape}" /></td>
<td><input type="submit" value="{tr}find{/tr}" name="search" /></td>
<td>{tr}Number of displayed rows{/tr}</td>
<td><input type="text" size="4" name="numrows" value="{$numrows|escape}">
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" /></td>
</tr></table>
</form>

<div align="center">
{section name=ini loop=$initials}
{if $initial and $initials[ini] eq $initial}
<span class="button2"><span class="linkbut">{$initials[ini]|capitalize}</span></span> . 
{else}
<a href="tiki-admingroups.php?initial={$initials[ini]}{if $find}&amp;find={$find|escape:"url"}{/if}{if $offset}&amp;offset={$offset}{/if}{if $numrows}&amp;numrows={$numrows}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}" 
class="prevnext">{$initials[ini]}</a> . 
{/if}
{/section}
<a href="tiki-admingroups.php?initial={if $find}&amp;find={$find|escape:"url"}{/if}{if $offset}&amp;offset={$offset}{/if}{if $numrows}&amp;numrows={$numrows}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}" 
class="prevnext">{tr}All{/tr}</a>
</div>

<table class="normal">
<tr>
<td class="heading" style="width: 20px;">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupName_desc'}groupName_asc{else}groupName_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}groupDesc_asc{else}groupDesc_desc{/if}">{tr}desc{/tr}</a></td>
<td class="heading">{tr}Includes{/tr}</td>
<td class="heading">{tr}Permissions{/tr}</td>
<td class="heading" style="width: 20px;">&nbsp;</td>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$users}
<tr class="{cycle}">
<td style="width: 20px;"><a class="link" href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}" title="{tr}Click here to edit this group{/tr}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a></td>
<td><a class="link" href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}" title="{tr}Click here to edit this group{/tr}">{$users[user].groupName}</a></td>
<td>{$users[user].groupDesc}</td>
<td>
{section name=ix loop=$users[user].included}
{$users[user].included[ix]}<br />
{/section}
</td>
<td>
{capture assign=over}{section name=grs loop=$users[user].perms}{$users[user].perms[grs]}(<a class="link"
href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$users[user].perms[grs]}&amp;group={$users[user].groupName|escape:"url"}&amp;action=remove">x</a>)<br />{/section}{/capture}
<a class="link" href="tiki-assignpermission.php?group={$users[user].groupName|escape:"url"}" title="{tr}Click here to assign permissions to this group{/tr}" {popup text="<br />$over"|escape:"javascript"|escape:"html"
sticky="true" caption="{tr}Permissions{/tr}" closetext="{tr}close{/tr}" right="true"}><img border="0" alt="{tr}Assign Permissions{/tr}" src="img/icons/key.gif" /> ({$smarty.section.grs.total})</a>
</td>
<td style="width: 20px;">
{if $users[user].groupName !== 'Anonymous'}<a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;group={$users[user].groupName|escape:"url"}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this group?{/tr}')" 
title="Click here to delete this group"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>{/if}
</td>
</tr>
{/section}
</table>
<br />
<div class="mini" align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admingroups.php?find={$find|escape:"url"}&amp;{if $initial}initial={$initial}&amp;{/if}offset={$prev_offset}&amp;sort_mode={$sort_mode}&amp;numrows={$numrows}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admingroups.php?find={$find|escape:"url"}&amp;{if $initial}initial={$initial}&amp;{/if}offset={$next_offset}&amp;sort_mode={$sort_mode}&amp;numrows={$numrows}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$numrows}
<a class="prevnext" href="tiki-admingroups.php?find={$find|escape:"url"}&amp;{if $initial}initial={$initial}&amp;{/if}offset={$selector_offset}&amp;sort_mode={$sort_mode}&amp;numrows={$numrows}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

{* ----------------------- tab with form --------------------------------------- *}
<div id="content{cycle name=content}" class="content">
{if $groupname eq ''}
<h3>{tr}Add New Group{/tr}</h3>
{else}
<h3>{tr}Edit this group:{/tr} {$groupname}</h3>
<a href="tiki-admingroups.php">{tr}Add new group{/tr}</a>
{/if}
<form action="tiki-admingroups.php" method="post">
<table class="normal">
<tr class="formcolor"><td><label for="groups_group">{tr}Group{/tr}:</label></td><td><input type="text" name="name" id="groups_group" value="{$groupname|escape}" /></td></tr>
<tr class="formcolor"><td><label for="groups_desc">{tr}Description{/tr}:</label></td><td><textarea rows="5" cols="20" name="desc" id="groups_desc">{$groupdesc}</textarea></td></tr>
<tr class="formcolor"><td><label for="groups_inc">{tr}Include{/tr}:</label></td><td>
<select name="include_groups[]" id="groups_inc" multiple="multiple" size="4">
{section name=ix loop=$users}
{assign var=inced value=$users[ix].groupName}
<option value="{$inced|escape}" {if $inc.$inced eq 'y'} selected="selected"{/if}>{$inced}</option>
{/section}
</select>
</td></tr>
<tr class="formcolor"><td><label for="groups_home">{tr}Home page{/tr}</label></td><td><input type="text" name="home" id="groups_home" value="{$grouphome|escape}" /></td></tr>
{if $groupTracker eq 'y' and $eligibleGroupTrackers}
<tr class="formcolor"><td><label for="groupTracker">{tr}Group Information Tracker{/tr}</label></td><td>
<select name="groupstracker" id="groupsTracker">
<option value="0">{tr}choose a group tracker ...{/tr}</option>
{foreach key=tid item=tit from=$trackers}
{if $eligibleGroupTrackers.$tid}
<option value="{$tid}"{if $tid eq $grouptrackerid} {assign var="ggr" value="$tit"}selected="selected"{/if}>{$tit}</option>
{/if}
{/foreach}
</select> <span class="button2"><a href="{if $grouptrackerid}tiki-admin_tracker_fields.php?trackerId={$grouptrackerid}{else}tiki-admin_trackers.php{/if}" class="linkbut">{tr}admin{/tr} {$ggr}</a>
</td></tr>
{/if}
{if $userTracker eq 'y'}
<tr class="formcolor"><td><label for="userstracker">{tr}Users Information Tracker{/tr}</label></td><td>
<select name="userstracker" id="usersTracker">
<option value="0">{tr}choose a users tracker ...{/tr}</option>
{foreach key=tid item=tit from=$trackers}
{if $eligibleUserTrackers.$tid}
<option value="{$tid}"{if $tid eq $userstrackerid} {assign var="ugr" value="$tit"}selected="selected"{/if}>{$tit}</option>
{/if}
{/foreach}
</select> <span class="button2"><a href="{if $grouptrackerid}tiki-admin_tracker_fields.php?trackerId={$userstrackerid}{else}tiki-admin_trackers.php{/if}" class="linkbut">{tr}admin{/tr} {$ugr}</a>
</td></tr>
{/if}
{if $group ne ''}
<tr class="formcolor"><td>&nbsp;
<input type="hidden" name="olgroup" value="{$group|escape}">
</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
{else}
<tr class="formcolor"><td >&nbsp;</td><td><input type="submit" name="newgroup" value="{tr}Add{/tr}" /></td></tr>
{/if}
</table>
</form>
<br /><br />

</div>

{* ----------------------- tab with more --------------------------------------- *}
{if $ins_fields}
<div id="content{cycle name=content}" class="content">
<h3>{tr}Additionnal information{/tr}</h3>
<table class="normal">
{section name=ix loop=$ins_fields}
{if $fields[ix].type eq 'h'}
</table>
<h3>{$fields[ix].label}</h3>
<table class="normal">
{elseif $fields[ix].type ne 'x'}
<tr class="formcolor"><td>{$fields[ix].label}</td>
<td>
{if $ins_fields[ix].type eq 'f' or $ins_fields[ix].type eq 'j'}
{$ins_fields[ix].value|date_format:$daformat}
{elseif $ins_fields[ix].type eq 'a'}
{$ins_fields[ix].pvalue}
{else}
{$ins_fields[ix].value}
{/if}
</td>
</tr>
{/if}
{/section}
</table>
</div>
{/if}

{* ----------------------- tab with memberlist --------------------------------------- *}
{if $memberslist}
<div id="content{cycle name=content}" class="content">
<h3>{tr}Members List{/tr}: {$groupname}</h3>
<table class="normal"><tr>
{cycle name=table values=',,,,</tr><tr>' print=false advance=false}
{section name=ix loop=$memberslist}
<td class="formcolor auto"><a href="tiki-adminusers.php?user={$memberslist[ix]|escape:"url"}" class="link">{$memberslist[ix]}</a></td>{cycle name=table}
{/section}
</tr></table>
<div class="box">{$smarty.section.ix.total} {tr}users in group{/tr} {$groupname}</div>
</div>
{/if}

