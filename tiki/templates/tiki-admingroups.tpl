{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admingroups.tpl,v 1.40 2004-02-14 01:00:11 mose Exp $ *}
{popup_init src="lib/overlib.js"}

<a class="pagetitle" href="tiki-admingroups.php">{tr}Admin groups{/tr}</a>
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=PermissionAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin groups{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' />{/if}
{if $feature_help eq 'y'}</a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admingroups.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin groups template{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' />{/if}
{if $feature_view_tpl eq 'y'}</a>{/if}
<br />

<span class="button2"><a href="tiki-admingroups.php" class="linkbut">{tr}Admin groups{/tr}</a></span>
<span class="button2"><a href="tiki-adminusers.php" class="linkbut">{tr}Admin users{/tr}</a></span>
{if $groupname}
<span class="button2"><a href="tiki-admingroups.php?add=1" class="linkbut">{tr}Add new group{/tr}</a></span>
{/if}
<br /><br /><br />

{cycle name=tabs values="1,2,3,4" print=false advance=false}
<div class="tabs">
<span id="tab{cycle name=tabs}" class="tab tabActive">{tr}List{/tr}</span>
{if $groupname}
<span id="tab{cycle name=tabs}" class="tab">{tr}Edit group{/tr} <i>{$groupname}</i></span>
{if $fields}
<span id="tab{cycle name=tabs}" class="tab">{tr}More info{/tr}</span>
{/if}
{if $memberslist}
<span id="tab{cycle name=tabs}" class="tab">{tr}Members{/tr}</span>
{/if}
{else}
<span id="tab{cycle name=tabs}" class="tab">{tr}Add a new group{/tr}</span>
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

{if $cant_pages > 1}
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
{/if}

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
<td style="width: 20px;"><a class="link" href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}" title="{tr}edit{/tr}"><img border="0" alt="{tr}edit{/tr}" src="img/icons/edit.gif" /></a></td>
<td><a class="link" href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}" title="{tr}edit{/tr}">{$users[user].groupName}</a></td>
<td>{$users[user].groupDesc}</td>
<td>
{section name=ix loop=$users[user].included}
{$users[user].included[ix]}<br />
{/section}
</td>
<td>
{capture assign=over}{section name=grs loop=$users[user].perms}{$users[user].perms[grs]}(<a class="link"
href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$users[user].perms[grs]}&amp;group={$users[user].groupName|escape:"url"}&amp;action=remove">x</a>)<br />{/section}{/capture}
<a class="link" href="tiki-assignpermission.php?group={$users[user].groupName|escape:"url"}" title="{tr}permissions{/tr}" {popup text="<br />$over"|escape:"javascript"|escape:"html"
sticky="true" caption="{tr}Permissions{/tr}" closetext="{tr}close{/tr}" right="true"}><img border="0" alt="{tr}permissions{/tr}" src="img/icons/key.gif" /> ({$smarty.section.grs.total})</a>
</td>
<td style="width: 20px;">
{if $users[user].groupName !== 'Anonymous'}<a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;group={$users[user].groupName|escape:"url"}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this group?{/tr}')" 
title="{tr}delete{/tr}"><img border="0" alt="{tr}demove{/tr}" src="img/icons2/delete.gif" /></a>{/if}
</td>
</tr>
{/section}
</table>
{if $cant_pages > 1}
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
{/if}
</div>

{* ----------------------- tab with form --------------------------------------- *}
<div id="content{cycle name=content}" class="content">
{if $groupname}
<h3>{tr}Edit group{/tr} {$groupname}</h3>
{else}
<h3>{tr}Add new group{/tr}</h3>
{/if}
<form action="tiki-admingroups.php" method="post">
<table class="normal">
<tr class="formcolor"><td><label for="groups_group">{tr}Group{/tr}:</label></td><td><input type="text" name="name" id="groups_group" value="{$groupname|escape}" /></td></tr>
<tr class="formcolor"><td><label for="groups_desc">{tr}Description{/tr}:</label></td><td><textarea rows="5" cols="20" name="desc" id="groups_desc">{$groupdesc}</textarea></td></tr>
<tr class="formcolor"><td><label for="groups_inc">{tr}Include{/tr}:</label></td><td>
<select name="include_groups[]" id="groups_inc" multiple="multiple" size="4">
{foreach key=gr item=yn from=$inc}
<option value="{$gr|escape}" {if $yn eq 'y'} selected="selected"{/if}>{$gr|truncate:"52":" ..."}</option>
{/foreach}
</select>
</td></tr>
<tr class="formcolor"><td><label for="groups_home">{tr}Home page{/tr}</label></td><td><input type="text" name="home" id="groups_home" value="{$grouphome|escape}" /></td></tr>
{if $groupTracker eq 'y' and $eligibleGroupTrackers}
<tr class="formcolor"><td><label for="groupTracker">{tr}Group Information Tracker{/tr}</label></td><td>
<select name="groupstracker" id="groupsTracker">
<option value="0">{tr}choose a group tracker ...{/tr}</option>
{foreach key=tid item=tit from=$trackers}
<option value="{$tid}"{if $tid eq $grouptrackerid} {assign var="ggr" value="$tit"}selected="selected"{/if}>{$tit}</option>
{/foreach}
</select> <span class="button2"><a href="{if $grouptrackerid}tiki-admin_tracker_fields.php?trackerId={$grouptrackerid}{else}tiki-admin_trackers.php{/if}" class="linkbut">{tr}admin{/tr} {$ggr}</a>
</td></tr>
{/if}
{if $userTracker eq 'y'}
<tr class="formcolor"><td><label for="userstracker">{tr}Users Information Tracker{/tr}</label></td><td>
<select name="userstracker" id="usersTracker">
<option value="0">{tr}choose a users tracker ...{/tr}</option>
{foreach key=tid item=tit from=$trackers}
<option value="{$tid}"{if $tid eq $userstrackerid} {assign var="ugr" value="$tit"}selected="selected"{/if}>{$tit}</option>
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
{if $groupname and $fields}
<div id="content{cycle name=content}" class="content">
<h3>{tr}Additionnal information{/tr}</h3>
<table class="normal">

{* copypaste from templates/tiki-view_tracker_item.tpl s/ins_fields/fields/g *}
{section name=ix loop=$fields}
{if $fields[ix].isPublic eq 'y' or $tiki_p_admin_trackers eq 'y'}

{if $fields[ix].type eq 'h'}
</table>
<h3>{$fields[ix].name}</h3>
<table class="normal">

{elseif $fields[ix].type ne 'x'}
{if $fields[ix].type eq 'c' or $fields[ix].type eq 't' and $fields[ix].options_array[0] eq '1'}
<tr class="formcolor"><td class="formlabel">{$fields[ix].name}</td><td>
{elseif $stick eq 'y'}
<td class="formlabel right">{$fields[ix].name}</td><td>
{else}
<tr class="formcolor"><td>{$fields[ix].name}</td>
<td colspan="3">
{/if}
{if $fields[ix].type eq 'f' or $fields[ix].type eq 'j'}
{$fields[ix].value|tiki_long_date}</td></tr>

{elseif $fields[ix].type eq 'a'}
{$fields[ix].pvalue}

{elseif $fields[ix].type eq 'e'}
{assign var=fca value=$fields[ix].options}
<table width="100%"><tr>{cycle name=$fca values=",</tr><tr>" advance=false print=false}
{foreach key=ku item=iu from=$fields[ix].$fca}
{assign var=fcat value=$iu.categId }
<td width="50%" nowrap="nowrap">
{if $fields[ix].cat.$fcat eq 'y'}
<tt>X&nbsp;</tt><b>{$iu.name}</b></td>
{else}
<tt>&nbsp;&nbsp;</tt><s>{$iu.name}</s></td>
{/if}
{cycle name=$fca}
{/foreach}
</tr></table></td></tr>


{elseif $fields[ix].type eq 'c'}
{$fields[ix].value|replace:"y":"{tr}Yes{/tr}"|replace:"n":"{tr}No{/tr}"}
{if $fields[ix].options_array[0] eq '1' and $stick ne 'y'}
</td>
{assign var=stick value="y"}
{else}
</td></tr>
{assign var=stick value="n"}
{/if}

{elseif $fields[ix].type eq 't'}
{if $fields[ix].options_array[2]}
{$fields[ix].value|default:"0"} <span class="formunit">&nbsp;{$fields[ix].options_array[2]}</span>
{else}
{$fields[ix].value}
{/if}
{if $fields[ix].options_array[0] eq '1' and $stick ne 'y'}
</td>
{assign var=stick value="y"}
{else}
</td></tr>
{assign var=stick value="n"}
{/if}

{else}
{$fields[ix].value}
{if $fields[ix].options_array[0] eq '1' and $stick ne 'y'}
</td>
{assign var=stick value="y"}
{else}
</td></tr>
{assign var=stick value="n"}
{/if}
{/if}
{/if}
{/if}
{/section}
{* end of copypasted block *}

</table>
<span class="button2"><a href="tiki-view_tracker_item.php?trackerId={$grouptrackerid}&amp;itemId={$groupitemId}&amp;show=mod" class="linkbut">{tr}Edit informations{/tr}</a></span>
</div>
{/if}

{* ----------------------- tab with memberlist --------------------------------------- *}
{if $groupname and $memberslist}
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

