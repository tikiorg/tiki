{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admingroups.tpl,v 1.84.2.8 2007-12-06 18:00:06 sylvieg Exp $ *}
{popup_init src="lib/overlib.js"}

<h1><a class="pagetitle" href="tiki-admingroups.php{if !empty($groupname)}?group={$groupname|escape:'url'}{/if}">{tr}Admin groups{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Permissions+Settings" target="tikihelp" class="tikihelp" title="{tr}Admin Groups{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admingroups.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Groups Template{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit template{/tr}' /></a>
{/if}
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=login" title="{tr}Admin Feature{/tr}">{html_image file='pics/icons/wrench.png' border='0'  alt="{tr}Admin Feature{/tr}"}</a>
{/if}
</h1>

<div class="navbar">
<span class="button2"><a href="tiki-admingroups.php" class="linkbut">{tr}Admin groups{/tr}</a></span>
<span class="button2"><a href="tiki-adminusers.php" class="linkbut">{tr}Admin users{/tr}</a></span>
<span class="button2"><a href="tiki-admingroups.php?clean=y" class="linkbut">{tr}Clear cache{/tr}</a></span>
{if $groupname}
<span class="button2"><a href="tiki-admingroups.php?add=1{if $prefs.feature_tabs ne 'y'}#2{/if}" class="linkbut">{tr}Add new group{/tr}</a></span>
{/if}
</div>

{if $prefs.feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3,4,5" print=false advance=false reset=true}
<div id="page-bar">
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $smarty.cookies.tab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},6);">{tr}List{/tr}</a></span>
{if $groupname}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $smarty.cookies.tab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},6);">{tr}Edit group{/tr} <i>{$groupname}</i></a></span>
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $smarty.cookies.tab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},6);">{tr}Members{/tr}</a></span>
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $smarty.cookies.tab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},6);">{tr}Import/Export{/tr}</a></span>
{else}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $smarty.cookies.tab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},6);">{tr}Add a new group{/tr}</a></span>
{/if}
</div>
{/if}

{cycle name=content values="1,2,3,4,5" print=false advance=false reset=true}
{* ----------------------- tab with list --------------------------------------- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<h2>{tr}List of existing groups{/tr}</h2>

<form method="get" action="tiki-admingroups.php">
<table class="findtable"><tr>
<td><label for="groups_find">{tr}Find{/tr}</label></td>
<td><input type="text" name="find" id="groups_find" value="{$find|escape}" /></td>
<td><input type="submit" value="{tr}Find{/tr}" name="search" /></td>
<td>{tr}Number of displayed rows{/tr}</td>
<td><input type="text" size="4" name="numrows" value="{$numrows|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" /></td>
</tr></table>
</form>

{if $cant_pages > 1 or !empty($initial) or !empty($find)}
<div align="center">
{section name=ini loop=$initials}
{if $initial and $initials[ini] eq $initial}
<span class="button2"><span class="linkbut">{$initials[ini]|capitalize}</span></span> . 
{else}
<a href="tiki-admingroups.php?initial={$initials[ini]}{if $find}&amp;find={$find|escape:"url"}{/if}{if $numrows}&amp;numrows={$numrows}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}" 
class="prevnext">{$initials[ini]}</a> . 
{/if}
{/section}
<a href="tiki-admingroups.php?initial={if $find}&amp;find={$find|escape:"url"}{/if}{if $numrows}&amp;numrows={$numrows}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}" 
class="prevnext">{tr}All{/tr}</a>
</div>
{/if}

<table class="normal">
<tr>
<td class="heading" style="width: 20px;">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupName_desc'}groupName_asc{else}groupName_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}groupDesc_asc{else}groupDesc_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading">{tr}Includes{/tr}</td>
<td class="heading">{tr}User Choice{/tr}</td>
<td class="heading">{tr}Permissions{/tr}</td>
<td class="heading" style="width: 20px;">&nbsp;</td>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$users}
<tr class="{cycle}">
<td style="width: 20px;"><a class="link" href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}" title="{tr}Edit{/tr}"><img src="pics/icons/page_edit.png" border="0" width="16" height="16" alt='{tr}Edit{/tr}' /></a></td>
<td><a class="link" href="tiki-admingroups.php?group={$users[user].groupName|escape:"url"}{if $prefs.feature_tabs ne 'y'}#2{/if}" title="{tr}Edit{/tr}">{$users[user].groupName}</a></td>
<td>{tr}{$users[user].groupDesc}{/tr}</td>
<td>
{section name=ix loop=$users[user].included}
{$users[user].included[ix]}<br />
{/section}
</td>
<td>{tr}{$users[user].userChoice}{/tr}</td>
<td>
<a class="link" href="tiki-assignpermission.php?group={$users[user].groupName|escape:"url"}" title="{tr}Permissions{/tr}"><img border="0" alt="{tr}Permissions{/tr}" src="pics/icons/key.png" width='16' height='16' /> {$users[user].permcant}</a>
</td>
<td style="width: 20px;">
{if $users[user].groupName ne 'Anonymous' and $users[user].groupName ne 'Registered' and $users[user].groupName ne 'Admins'}<a class="link" href="tiki-admingroups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;group={$users[user].groupName|escape:"url"}" title="{tr}Delete{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="pics/icons/cross.png" width='16' height='16' /></a>{/if}
</td>
</tr>
{/section}
</table>
{if $cant_pages > 1}
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admingroups.php?find={$find|escape:"url"}&amp;{if $initial}initial={$initial}&amp;{/if}offset={$prev_offset}&amp;sort_mode={$sort_mode}&amp;numrows={$numrows}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admingroups.php?find={$find|escape:"url"}&amp;{if $initial}initial={$initial}&amp;{/if}offset={$next_offset}&amp;sort_mode={$sort_mode}&amp;numrows={$numrows}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
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
<a name="2" ></a>
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
{if $groupname}
<h2>{tr}Edit group{/tr} {$groupname}</h2>
<a class="linkbut" href="tiki-assignpermission.php?group={$groupname}">{tr}Assign Permissions{/tr}</a>
{else}
<h2>{tr}Add new group{/tr}</h2>
{/if}
<form action="tiki-admingroups.php" method="post">
<table class="normal">
<tr class="formcolor"><td><label for="groups_group">{tr}Group{/tr}:</label></td><td>{if $groupname neq 'Anonymous' and $groupname neq 'Registered' and $groupname neq 'Admins'}<input type="text" name="name" id="groups_group" value="{$groupname|escape}" />{else}<input type="hidden" name="name" id="groups_group" value="{$groupname|escape}" />{$groupname}{/if}</td></tr>
<tr class="formcolor"><td><label for="groups_desc">{tr}Description{/tr}:</label></td><td><textarea rows="5" cols="20" name="desc" id="groups_desc">{$groupdesc}</textarea></td></tr>
<tr class="formcolor"><td><label for="groups_inc">{tr}Include{/tr}:</label><br /><i>{tr}Only directly included{/tr}<br />{tr}The group will have all the permissions of the included groups{/tr}</i></td><td>
{if $inc|@count > 20 and $hasOneIncludedGroup eq "y"}
{foreach key=gr item=yn from=$inc}{if $yn eq 'y'}{$gr|escape} {/if}{/foreach}<br />
{/if}
<select name="include_groups[]" id="groups_inc" multiple="multiple" size="4">
{if !empty($groupname)}<option value="">{tr}None{/tr}</option>{/if}
{foreach key=gr item=yn from=$inc}
<option value="{$gr|escape}" {if $yn eq 'y'} selected="selected"{/if}>{$gr|truncate:"52":" ..."}</option>
{/foreach}
</select>
</td></tr>
<tr class="formcolor"><td><label for="groups_home">{tr}Group Homepage{/tr}:<br />
({tr}Use wiki page name or full URL{/tr})<br />
{tr}To use a relative link, use ex.{/tr}: <i>http:tiki-forums.php</i>
</label></td><td><input type="text" size="40" name="home" id="groups_home" value="{$grouphome|escape}" /></td></tr>
{if $prefs.feature_categories eq 'y'}
<tr class="formcolor"><td><label for="groups_defcat">{tr}Default category assigned to uncategorized objects edited by a user with this default group{/tr}:</label>
  	 </td><td>
  	 
  	 <select name="defcat" id="groups_defcat" size="4">
  	 <option value="" {if ($groupdefcat eq "") or ($groupdefcat eq 0)} selected="selected"{/if}>{tr}none{/tr}</option>
  	         {section name=ix loop=$categories}
  	         <option value="{$categories[ix].categId|escape}" {if $categories[ix].categId eq $groupdefcat}selected="selected"{/if}>{$categories[ix].categpath}</option>
  	         {/section}
  	 </select>
  	 </td></tr>
{/if} 	 
  	 <tr class="formcolor"><td><label for="groups_theme">{tr}Group Theme{/tr}:</label>
  	 </td><td>
  	 <select name="theme" id="groups_theme" multiple="multiple" size="4">
  	 <option value="" {if $grouptheme eq ""} selected="selected"{/if}>{tr}none{/tr}</option>
  	             {section name=ix loop=$av_themes}
  	               <option value="{$av_themes[ix]|escape}"
  	                 {if $grouptheme eq $av_themes[ix]}selected="selected"{/if}>
  	                 {$av_themes[ix]}</option>
  	             {/section}
  	 </select>
  	 </td></tr>
{if $prefs.groupTracker eq 'y'}
<tr class="formcolor"><td><label for="groupTracker">{tr}Group Information Tracker{/tr}</label></td><td>
<select name="groupstracker">
<option value="0">{tr}choose a group tracker ...{/tr}</option>
{foreach key=tid item=tit from=$trackers}
<option value="{$tid}"{if $tid eq $grouptrackerid} {assign var="ggr" value="$tit"}selected="selected"{/if}>{$tit}</option>
{/foreach}
</select>
{if $grouptrackerid}
<br />
<select name="groupfield">
<option value="0">{tr}choose a field ...{/tr}</option>
{section name=ix loop=$groupFields}
<option value="{$groupFields[ix].fieldId}"{if $groupFields[ix].fieldId eq $groupfieldid} selected="selected"{/if}>{$groupFields[ix].name}</option>
{/section}
</select>
{/if}
<span class="button2"><a href="{if $grouptrackerid}tiki-admin_tracker_fields.php?trackerId={$grouptrackerid}{else}tiki-admin_trackers.php{/if}" class="linkbut">{tr}Admin{/tr} {$ggr}</a>
</td></tr>
{/if}
{if $prefs.userTracker eq 'y'}
<tr class="formcolor"><td><label for="userstracker">{tr}Users Information Tracker{/tr}</label></td><td>
<select name="userstracker">
<option value="0">{tr}choose a users tracker ...{/tr}</option>
{foreach key=tid item=tit from=$trackers}
<option value="{$tid}"{if $tid eq $userstrackerid} {assign var="ugr" value="$tit"}selected="selected"{/if}>{$tit}</option>
{/foreach}
</select>
{if $userstrackerid}
<br />
<select name="usersfield">
<option value="0">{tr}choose a field ...{/tr}</option>
{section name=ix loop=$usersFields}
<option value="{$usersFields[ix].fieldId}"{if $usersFields[ix].fieldId eq $usersfieldid} selected="selected"{/if}>{$usersFields[ix].fieldId} - {$usersFields[ix].name}</option>
{/section}
</select>
{/if}
<span class="button2"><a href="{if $grouptrackerid}tiki-admin_tracker_fields.php?trackerId={$userstrackerid}{else}tiki-admin_trackers.php{/if}" class="linkbut">{tr}Admin{/tr} {$ugr}</a>
</td></tr>
<tr class="formcolor"><td>{tr}Users Information Tracker Fields Asked at Registration Time<br />(fieldIds separated with :){/tr}</td>
<td><input type="text" size="40" name="registrationUsersFieldIds" value="{$registrationUsersFieldIds|escape}" /></td></tr>
{/if}
<tr class="formcolor"><td>{tr}User can assign to the group himself{/tr}</td><td><input type="checkbox" name="userChoice"{if $userChoice eq 'y'} checked="checked"{/if}></td></tr>
{if $group ne ''}
<tr class="formcolor"><td>&nbsp;
<input type="hidden" name="olgroup" value="{$group|escape}" />
</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
{else}
<tr class="formcolor"><td >&nbsp;</td><td><input type="submit" name="newgroup" value="{tr}Add{/tr}" /></td></tr>
{/if}
</table>
</form>
<br /><br />

{if $prefs.groupTracker eq 'y'}
{if $grouptrackerid and $groupitemid}
{tr}Group tracker item : {$groupitemid}{/tr} <span class="button2"><a href="tiki-view_tracker_item.php?trackerId={$grouptrackerid}&amp;itemId={$groupitemid}&amp;show=mod" class="linkbut">{tr}Edit item{/tr}</a></span>
{elseif $grouptrackerid}
{if $groupfieldid}
{tr}Group tracker item not found{/tr} <span class="button2"><a href="tiki-view_tracker.php?trackerId={$grouptrackerid}" class="linkbut">{tr}Create item{/tr}</a></span>
{else}
{tr}choose a field ...{/tr}
{/if}
{else}
{tr}choose a group tracker ...{/tr}
{/if}
<br /><br />
{/if}
</div>

{* ----------------------- tab with memberlist --------------------------------------- *}
<a name="3" ></a>
{if $groupname}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<h2>{tr}Members List{/tr}: {$groupname}</h2>
<table class="normal"><tr>
{cycle name=table values=',,,,</tr><tr>' print=false advance=false}
{section name=ix loop=$memberslist}
<td class="formcolor auto"><a href="tiki-adminusers.php?user={$memberslist[ix]|escape:"url"}&action=removegroup&group={$groupname}{if $prefs.feature_tabs ne 'y'}#2{/if}" class="link" title="{tr}Remove from Group{/tr}"><img src="pics/icons/cross.png" border="0" width="16" height="16"  alt='{tr}Remove{/tr}'></a> <a href="tiki-adminusers.php?user={$memberslist[ix]|escape:"url"}{if $prefs.feature_tabs ne 'y'}#2{/if}" class="link" title="{tr}Edit{/tr}"><img src="pics/icons/page_edit.png" border="0" width="16" height="16" alt='{tr}Edit{/tr}'></a> {$memberslist[ix]|userlink}</td>{cycle name=table}
{/section}
</tr></table>
<div class="box">{$smarty.section.ix.total} {tr}users in group{/tr} {$groupname}</div>
</div>
{/if}

{* ----------------------- tab with import/export --------------------------------------- *}
<a name="4" ></a>
{if $groupname}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<form method="post" action="tiki-admingroups.php"  enctype="multipart/form-data">
<input type="hidden" name="group" value="{$groupname|escape}" />

{if $errors}
<div class="simple highlight">
{foreach from=$errors item=e}
{$e}</br />
{/foreach}
</div>
{/if}

<h2>{tr}Download CSV export{/tr}</h2>
<table class="normal">
<tr>
<td class="formcolor auto">{tr}Charset encoding:{/tr}</td><td  class="formcolor auto"><select name="encoding"><option value="UTF-8" selected="selected">{tr}UTF-8{/tr}</option><option value="ISO-8859-1">{tr}ISO-8859-1{/tr}</option></select></td>
</tr><tr>
<td class="formcolor auto"></td><td class="formcolor auto"><input type="checkbox" name="username" checked="checked" />{tr}Username{/tr}<br /><input type="checkbox" name="email">{tr}Email{/tr}</td>
</tr><tr>
<td class="formcolor auto"></td><td class="formcolor auto"><input type="submit" name="export" value="{tr}Export{/tr}" /></td>
</tr>
</table>

<h2>{tr}Batch upload (CSV file):{/tr}</h2>
{tr}Assign users to group:{/tr} {$groupname} <br />{tr}User must already exist.{/tr}<br />{tr}To create users and assign them to groups, got to admin->users{/tr}
<table class="normal">
<tr>
<td class="formcolor auto">{tr}CSV File{/tr}<a {popup text='user<br />user1<br />user2'}><img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a></td><td class="formcolor auto"><input name="csvlist" type="file" /></td>
</tr><tr>
<td class="formcolor auto"></td><td class="formcolor auto"><input type="submit" name="import" value="{tr}Import{/tr}" /></td>
</tr>
</table>
</form>
</div>
{/if}
