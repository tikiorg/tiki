<a class="pagetitle" href="tiki-admin_events.php">{tr}Admin events{/tr}</a>

  
      {if $feature_help eq 'y'}
<a href="{$helpurl}Events" target="tikihelp" class="tikihelp" title="{tr}Events{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}



      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_events.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}admin events template{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit{/tr}' /></a>{/if}



<br /><br />
<a class="linkbut" href="tiki-events.php">{tr}list events{/tr}</a>
<a class="linkbut" href="tiki-send_events.php">{tr}send events{/tr}</a>
<br /><br />
<h2>{tr}Create/edit events{/tr}</h2>
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName={$info.name|escape:"url"}&amp;objectType=event&amp;permType=events&amp;objectId={$info.evId}">{tr}There are individual permissions set for this event{/tr}</a><br /><br />
{/if}
<form action="tiki-admin_events.php" method="post">
<input type="hidden" name="evId" value="{$info.evId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$info.name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$info.description|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Users can subscribe/unsubscribe to this list{/tr}</td><td class="formcolor">
<input type="checkbox" name="allowUserSub" {if $info.allowUserSub eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Users can subscribe any email address{/tr}</td><td class="formcolor">
<input type="checkbox" name="allowAnySub" {if $info.allowAnySub eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Add unsubscribe instructions to each event{/tr}</td><td class="formcolor">
<input type="checkbox" name="unsubMsg" {if $info.unsubMsg eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Validate email addresses{/tr}</td><td class="formcolor">
<input type="checkbox" name="validateAddr" {if $info.validateAddr eq 'y'}checked="checked"{/if} /></td></tr>
{* <tr><td class="formcolor">{tr}Frequency{/tr}</td><td class="formcolor">
<select name="frequency">
{section name=ix loop=$freqs}
<option value="{$freqs[ix].t|escape}" {if $info.frequency eq $freqs[ix].t}selected="selected"{/if}>{$freqs[ix].i} {tr}days{/tr}</option>
{/section}
</select>
</td></tr>
*}
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Events{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_events.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td>&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-admin_events.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'evId_desc'}evId_asc{else}evId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_events.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_events.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_events.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}">{tr}users{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_events.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'editions_desc'}editions_asc{else}editions_desc{/if}">{tr}editions{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_events.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastSent_desc'}lastSent_asc{else}lastSent_desc{/if}">{tr}last sent{/tr}</a></td>
<td>&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">
<a class="link" href="tiki-admin_events.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].evId}" title="{tr}remove{/tr}"><img border="0" src="img/icons2/delete.gif" alt="{tr}remove{/tr}" /></a>
</td>
<td class="{cycle advance=false}">{$channels[user].evId}</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-admin_events.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;evId={$channels[user].evId}" title="{tr}edit{/tr}">{$channels[user].name}</a></td>
<td class="{cycle advance=false}">{$channels[user].description}</td>
<td class="{cycle advance=false}">{$channels[user].users} ({$channels[user].confirmed})</td>
<td class="{cycle advance=false}">{$channels[user].editions}</td>
<td class="{cycle advance=false}">{$channels[user].lastSent|tiki_short_datetime}</td>
<td class="{cycle}">
<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=event&amp;permType=events&amp;objectId={$channels[user].evId}" title="{tr}Assign Permissions{/tr}"><img 
border="0" alt="{tr}Assign Permissions{/tr}" src="img/icons/key{if $channels[user].individual eq 'y'}_active{/if}.gif" /></a>&nbsp;
<a class="link" href="tiki-admin_events.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;evId={$channels[user].evId}" title="{tr}edit{/tr}"><img border="0" src="img/icons/edit.gif" alt="{tr}edit{/tr}" /></a>
<a class="link" href="tiki-admin_event_subscriptions.php?evId={$channels[user].evId}" title="{tr}subscriptions{/tr}"><img border="0" src="img/icons2/icn_members.gif" alt="{tr}subscriptions{/tr}" /></a>&nbsp;&nbsp;
<a class="link" href="tiki-send_events.php?evId={$channels[user].evId}" title="{tr}send event{/tr}"><img border="0" src="img/icons/email.gif" alt="{tr}send event{/tr}" /></a>
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_events.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_events.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_events.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

