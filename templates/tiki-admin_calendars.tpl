<h1><a class="pagetitle" href="tiki-admin_calendars.php">{tr}Admin Calendars{/tr}</a>
{if $tiki_p_admin eq 'y'}
<a title="{tr}Configure/Options{/tr}" href="tiki-admin.php?page=calendar">{html_image file='img/icons/config.gif' border='0' alt='{tr}Configure/Options{/tr}'}</a>
{/if} 
</h1>
{* {if $feature_tabs eq 'y'}
<div class="tabs">
<span id="tab1" class="tab tabActive">{tr}List Calendars{/tr}</span>
<span id="tab2" class="tab">{tr}Create/edit Calendars{/tr}</span>
</div>
{/if} *}

{* --- tab with list --- *}
<div id="content1" class="content">
<h2>{tr}List of Calendars{/tr}</h2>
{if count($calendars) gt 0}
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_calendars.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'calendarId_desc'}calendarId_asc{else}calendarId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlocations_desc'}customlocations_asc{else}customlocations_desc{/if}">{tr}loc{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customparticipants_desc'}customparticipants_asc{else}customparticipants_desc{/if}">{tr}participants{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customcategories_desc'}customcategories_asc{else}customcategories_desc{/if}">{tr}cat{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlanguages_desc'}customlanguages_asc{else}customlanguages_desc{/if}">{tr}lang{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'custompriorities_desc'}custompriorities_asc{else}custompriorities_desc{/if}">{tr}prio{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customsubscription_desc'}customsubscription_asc{else}customsubscription_desc{/if}">{tr}subscription{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'personal_desc'}personal_asc{else}personal_desc{/if}">{tr}perso{/tr}</a></td>
<td class="heading">&nbsp;</td>
<td class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{foreach key=id item=cal from=$calendars}
<tr class="{cycle}">
<td>{$id}</td>
<td><a class="tablename" href="tiki-calendar.php?calIds[]={$id}">{$cal.name}</a></td>
<td>{$cal.customlocations}</td>
<td>{$cal.customparticipants}</td>
<td>{$cal.customcategories}</td>
<td>{$cal.customlanguages}</td>
<td>{$cal.custompriorities}</td>
<td>{$cal.customsubscription}</td>
<td>{$cal.personal}</td>
<td>
<a title="{tr}permissions{/tr}" class="link" 
href="tiki-objectpermissions.php?objectName={$cal.name|escape:"url"}&amp;objectType=calendar&amp;permType=calendar&amp;objectId={$id}"><img 
src='img/icons/key.gif' border='0' alt='{tr}permissions{/tr}' />{if $cal.individual gt 0} {$cal.individual}</a>{/if}</td>
<td>
   &nbsp;&nbsp;<a title="{tr}delete{/tr}" class="link" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;drop={$id}" 
title="{tr}delete{/tr}"><img src="img/icons2/delete.gif" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>&nbsp;&nbsp;
   <a title="{tr}edit{/tr}" class="link" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;calendarId={$id}"><img src="img/icons/edit.gif" border="0" width="20" height="16"  alt='{tr}edit{/tr}' /></a>
</td>
</tr>
{/foreach}
</table>
<br />

<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_calendars.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_calendars.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_calendars.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

</div>
{else}
<b>{tr}No records found{/tr}</b>
{/if}
</div>

{* --- tab with form --- *}
<div id="content2" class="content">
<h2>{tr}Create/edit Calendars{/tr}</h2>

<form action="tiki-admin_calendars.php" method="post">
<input type="hidden" name="calendarId" value="{$calendarId|escape}" />
<table class="normal">
{if $tiki_p_view_categories eq 'y'}
{include file=categorize.tpl}
{/if}
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="5" wrap="virtual" style="width:100%;">{$description|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Custom Locations{/tr}:</td><td class="formcolor">
<select name="customlocations">
<option value='y' {if $customlocations eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $customlocations eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Custom Participants{/tr}:</td><td class="formcolor">
<select name="customparticipants">
<option value='y' {if $customparticipants eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $customparticipants eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Custom Categories{/tr}:</td><td class="formcolor">
<select name="customcategories">
<option value='y' {if $customcategories eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $customcategories eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Custom Languages{/tr}:</td><td class="formcolor">
<select name="customlanguages">
<option value='y' {if $customlanguages eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $customlanguages eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
{if $feature_newsletters eq 'y'}
<tr><td class="formcolor">{tr}Custom Subscription List{/tr}:</td><td class="formcolor">
<select name="customsubscription">
<option value='y' {if $customsubscription eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $customsubscription eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
{/if}
<tr><td class="formcolor">{tr}Custom Priorities{/tr}:</td><td class="formcolor">
<select name="custompriorities">
<option value='y' {if $custompriorities eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $custompriorities eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Personal Calendar{/tr}:</td><td class="formcolor">
<select name="personal">
<option value='y' {if $personal eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $personal eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

</div>
