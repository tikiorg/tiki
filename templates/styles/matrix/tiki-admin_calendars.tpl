<a class="pagetitle" href="tiki-admin_calendars.php">{tr}Admin Calendars{/tr}</a><br/>
<h2>{tr}Create/edit Calendars{/tr}</h2>

<form action="tiki-admin_calendars.php" method="post">
<input type="hidden" name="calendarId" value="{$calendarId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="5" cols="80" wrap="virtual">{$description}</textarea></td></tr>
<tr><td class="formcolor">{tr}Custom Locations{/tr}:</td><td class="formcolor">
<select name="customlocations">
<option value='y' {if $customlocations eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $customlocations eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
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
<tr><td class="formcolor">{tr}Custom Priorities{/tr}:</td><td class="formcolor">
<select name="custompriorities">
<option value='y' {if $custompriorities eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $custompriorities eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

{if count($calendars) gt 0}
<h2>{tr}List of Calendars{/tr}</h2>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_calendars.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'calendarId_desc'}calendarId_asc{else}calendarId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlocations_desc'}customlocations_asc{else}customlocations_desc{/if}">{tr}loc{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customcategories_desc'}customcategories_asc{else}customcategories_desc{/if}">{tr}cat{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlanguages_desc'}customlanguages_asc{else}customlanguages_desc{/if}">{tr}lang{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'custompriorities_desc'}custompriorities_asc{else}custompriorities_desc{/if}">{tr}prio{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{foreach key=id item=cal from=$calendars}
<tr>
<td class="{cycle advance=false}">{$id}</td>
<td class="{cycle advance=false}"><a class="tablename" href="tiki-calendar.php?calIds[]={$id}">{$cal.name}</a></td>
<td class="{cycle advance=false}">{$cal.customlocations}</td>
<td class="{cycle advance=false}">{$cal.customcategories}</td>
<td class="{cycle advance=false}">{$cal.customlanguages}</td>
<td class="{cycle advance=false}">{$cal.custompriorities}</td>
<td class="{cycle}">
   <a class="link" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&drop={$id}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&calendarId={$id}">{tr}edit{/tr}</a>
</td>
</tr>
{/foreach}
</table>
<br/>
</div>
</div>
{/if}