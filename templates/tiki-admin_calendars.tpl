<h1><a class="pagetitle" href="tiki-admin_calendars.php">{tr}Admin Calendars{/tr}</a>
{if $tiki_p_admin eq 'y'}
<a title="{tr}Configure/Options{/tr}" href="tiki-admin.php?page=calendar">{icon _id='wrench' alt='{tr}Configure/Options{/tr}'}</a>
{/if} 
</h1>
{* {if $prefs.feature_tabs eq 'y'}
<div class="tabs">
<span id="tab1" class="tab tabActive">{tr}List Calendars{/tr}</span>
<span id="tab2" class="tab">{tr}Create/edit Calendars{/tr}</span>
</div>
{/if} *}

{* --- tab with list --- *}
<div id="content1" class="content">
<h2>{tr}List of Calendars{/tr}</h2>
{if count($calendars) gt 0}
{include file='find.tpl' _sort_mode='y'}

<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'calendarId_desc'}calendarId_asc{else}calendarId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlocations_desc'}customlocations_asc{else}customlocations_desc{/if}">{tr}Loc{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customparticipants_desc'}customparticipants_asc{else}customparticipants_desc{/if}">{tr}Participants{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customcategories_desc'}customcategories_asc{else}customcategories_desc{/if}">{tr}Cat{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlanguages_desc'}customlanguages_asc{else}customlanguages_desc{/if}">{tr}Lang{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customurl_desc'}customurl_asc{else}customurl_desc{/if}">{tr}Url{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'custompriorities_desc'}custompriorities_asc{else}custompriorities_desc{/if}">{tr}Prio{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customsubscription_desc'}customsubscription_asc{else}customsubscription_desc{/if}">{tr}Subscription{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'personal_desc'}personal_asc{else}personal_desc{/if}">{tr}Perso{/tr}</a></td>
<td class="heading">&nbsp;</td>
<td class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{foreach key=id item=cal from=$calendars}
<tr class="{cycle}">
<td>{$id}</td>
<td><a class="tablename" href="tiki-calendar.php?calIds[]={$id}">{$cal.name}</a>{if $cal.show_calname eq 'y'} {icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}</td>
<td>{$cal.customlocations}{if $cal.show_location eq 'y'}{icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}</td>
<td>{$cal.customparticipants}{if $cal.show_participants eq 'y'}{icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}</td>
<td>{$cal.customcategories}{if $cal.show_category eq 'y'}{icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}</td>
<td>{$cal.customlanguages}{if $cal.show_language eq 'y'}{icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}</td>
<td>{$cal.customurl}{if $cal.show_url eq 'y'}{icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}</td>
<td>{$cal.custompriorities}</td>
<td>{$cal.customsubscription}</td>
<td>{$cal.personal}</td>
<td>
<a title="{tr}Permissions{/tr}" class="link" 
href="tiki-objectpermissions.php?objectName={$cal.name|escape:"url"}&amp;objectType=calendar&amp;permType=calendar&amp;objectId={$id}">{if $cal.individual gt 0}{icon _id='key_active' alt='{tr}Permissions{/tr}'}</a>{$cal.individual}{else}{icon _id='key' alt='{tr}Permissions{/tr}'}</a>{/if}</td>
<td>
   &nbsp;&nbsp;<a title="{tr}Edit{/tr}" class="link" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;calendarId={$id}">{icon _id='page_edit'}</a> &nbsp;
   <a title="{tr}Delete{/tr}" class="link" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;drop={$id}" 
   title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
</td>
</tr>
{/foreach}
</table>
<br />

<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_calendars.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_calendars.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_calendars.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
{else}
<b>{tr}No records found{/tr}</b>
{/if}
</div>
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
<tr class="formcolor"><td>{tr}Name{/tr}:</td><td><input type="text" name="name" value="{$name|escape}" />
{tr}Show in popup box{/tr}
<input type="checkbox" name="show[calname]" value="on"{if $show_calname eq 'y'} checked="checked"{/if} />
</td></tr>
<tr class="formcolor"><td>{tr}Description{/tr}:</td><td><textarea name="description" rows="5" wrap="virtual" style="width:100%;">{$description|escape}</textarea>
<br />
{tr}Show in popup box{/tr}
<input type="checkbox" name="show[description]" value="on"{if $show_description eq 'y'} checked="checked"{/if} />
</td></tr>
<tr class="formcolor"><td>{tr}Custom Locations{/tr}:</td><td>
<select name="customlocations">
<option value='y' {if $customlocations eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n' {if $customlocations eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
</select>
{tr}Show in popup box{/tr}
<input type="checkbox" name="show[location]" value="on"{if $show_location eq 'y'} checked="checked"{/if} />
</td></tr>
<tr class="formcolor"><td>{tr}Custom Participants{/tr}:</td><td>
<select name="customparticipants">
<option value='y' {if $customparticipants eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n' {if $customparticipants eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
</select>
{tr}Show in popup box{/tr}
<input type="checkbox" name="show[participants]" value="on"{if $show_participants eq 'y'} checked="checked"{/if} />
</td></tr>
<tr class="formcolor"><td>{tr}Custom Categories{/tr}:</td><td>
<select name="customcategories">
<option value='y' {if $customcategories eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n' {if $customcategories eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
</select>
{tr}Show in popup box{/tr}
<input type="checkbox" name="show[category]" value="on"{if $show_category eq 'y'} checked="checked"{/if} />
</td></tr>
<tr class="formcolor"><td>{tr}Custom Languages{/tr}:</td><td>
<select name="customlanguages">
<option value='y' {if $customlanguages eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n' {if $customlanguages eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
</select>
{tr}Show in popup box{/tr}
<input type="checkbox" name="show[language]" value="on"{if $show_language eq 'y'} checked="checked"{/if} />
</td></tr>
<tr class="formcolor"><td>{tr}Custom URL{/tr}:</td><td>
<select name="options[customurl]">
<option value='y' {if $customurl eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n' {if $customurl eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
</select>
{tr}Show in popup box{/tr}
<input type="checkbox" name="show[url]" value="on"{if $show_url eq 'y'} checked="checked"{/if} />
</td></tr>
{if $prefs.feature_newsletters eq 'y'}
<tr class="formcolor"><td>{tr}Custom Subscription List{/tr}:</td><td>
<select name="customsubscription">
<option value='y' {if $customsubscription eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n' {if $customsubscription eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
</select>
</td></tr>
{/if}
<tr class="formcolor"><td>{tr}Custom Priorities{/tr}:</td><td>
<select name="custompriorities">
<option value='y' {if $custompriorities eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n' {if $custompriorities eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
</select>
</td></tr>
<tr class="formcolor"><td>{tr}Personal Calendar{/tr}:</td><td>
<select name="personal">
<option value='y' {if $personal eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value='n' {if $personal eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
</select>
</td></tr>
<tr class="formcolor"><td>{tr}Start of day{/tr}:</td><td>
<select name="startday_Hour">{foreach item=h from=$hours}<option value="{$h}"{if $h eq $startday} selected="selected"{/if}>{$h}</option>{/foreach}</select>{tr}h{/tr}
</td></tr>
<tr class="formcolor"><td>{tr}End of day{/tr}:</td><td>
<select name="endday_Hour">{foreach item=h from=$hours}<option value="{$h}"{if $h eq $endday} selected="selected"{/if}>{$h}</option>{/foreach}</select>{tr}h{/tr}
</td></tr>
<tr class="formcolor"><td>{tr}Custom foreground color{/tr}:</td><td>
<input type="text" name="options[customfgcolor]" value="{$customfgcolor}" size="6" />
</td></tr>
<tr class="formcolor"><td>{tr}Custom background color{/tr}:</td><td>
<input type="text" name="options[custombgcolor]" value="{$custombgcolor}" size="6" />
</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
<br />
{if $calendarId}{$name} : {/if}
{tr}Delete events older than:{/tr} <input type="text" name="days" value="0"/> {tr}days{/tr} <input type="submit" name="clean" value="{tr}Delete{/tr}" />
</form>

</div>
