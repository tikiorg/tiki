<a class="pagetitle" href="tiki-admin_calendars.php">{tr}Admin Calendars{/tr}</a><br/>
<h2>{tr}Create/edit Calendars{/tr}</h2>

<form action="tiki-admin_calendars.php" method="post">
<input type="hidden" name="calendarId" value="{$calendarId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="5" cols="80" wrap="virtual">{$description}</textarea></td></tr>
<tr><td class="formcolor">{tr}Public{/tr}:</td><td class="formcolor">
<select name="public">
<option value='y' {if $public eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $public eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Visible{/tr}:</td><td class="formcolor">
<select name="visible">
<option value='y' {if $visible eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value='n' {if $visible eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>


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
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'public_desc'}public_asc{else}public_desc{/if}">{tr}public{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'visible_desc'}visible_asc{else}visible_desc{/if}">{tr}visible{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=cal loop=$calendars}
<tr>
<td class="{cycle advance=false}">{$calendars[cal].calendarId}</td>
<td class="{cycle advance=false}"><a class="tablename" href="tiki-calendar.php?calIds[]={$calendars[cal].calendarId}">{$calendars[cal].name}</a></td>
<td class="{cycle advance=false}">{$calendars[cal].public}</td>
<td class="{cycle advance=false}">{$calendars[cal].visible}</td>
<td class="{cycle}">
   <a class="link" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&drop={$calendars[cal].calendarId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&calendarId={$calendars[cal].calendarId}">{tr}edit{/tr}</a>
</td>
</tr>
{/section}
</table>
<br/>
</div>
</div>

