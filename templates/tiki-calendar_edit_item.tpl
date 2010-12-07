{strip}
{title admpage="calendar"}{tr}Calendar Item{/tr}{/title}

<div class="navbar">
{if $tiki_p_view_calendar eq 'y'}
	{button href="tiki-calendar.php" _text="{tr}View Calendars{/tr}"}
{/if}
{if $tiki_p_admin_calendar eq 'y'}
	{button href="tiki-admin_calendars.php?calendarId=$calendarId" _text="{tr}Edit Calendar{/tr}"}
{/if}
{if $tiki_p_add_events eq 'y' and $id }
	{button href="tiki-calendar_edit_item.php" _text="{tr}New event{/tr}"}
{/if}
{if $id}
	{if $edit}
		{button href="tiki-calendar_edit_item.php?viewcalitemId=$id" _text="{tr}View event{/tr}"}
	{elseif $tiki_p_change_events eq 'y'}
		{button href="tiki-calendar_edit_item.php?calitemId=$id" _text="{tr}Edit/Delete event{/tr}"}
	{/if}
{/if}
{if $tiki_p_admin_calendar eq 'y'}
	{button href="tiki-admin_calendars.php" _text="{tr}Admin Calendars{/tr}"}
{/if}
</div>

<div class="wikitext">

{if $edit}
	{if $preview}
		<h2>{tr}Preview{/tr}</h2>
		{$calitem.parsedName}
		<div class="wikitext">{$calitem.parsed}</div>
		<h2>{if $id}{tr}Edit Calendar Item{/tr}{else}{tr}New Calendar Item{/tr}{/if}</h2>
	{/if}
	<form action="{$myurl}" method="post" name="f" id="editcalitem">
		<input type="hidden" name="save[user]" value="{$calitem.user}" />
		{if $id}
			<input type="hidden" name="save[calitemId]" value="{$id}" />
		{/if}
{/if}
{if $prefs.calendar_addtogooglecal == 'y'}
	{wikiplugin _name="addtogooglecal" calitemid=$id}{/wikiplugin}
{/if}
<table class="formcolor{if !$edit} vevent{/if}">
<tr>
	<td>{tr}Calendar{/tr}</td>
	<td style="background-color:#{$calendar.custombgcolor};color:#{$calendar.customfgcolor};">
{if $edit}
	{if $prefs.javascript_enabled eq 'n'}
		{$calendar.name|escape}<br />{tr}or{/tr}&nbsp;
		<input type="submit" name="changeCal" value="{tr}Go to{/tr}" />
	{/if}
		<select name="save[calendarId]" id="calid" onchange="javascript:document.getElementById('editcalitem').submit();">
			{foreach item=it key=itid from=$listcals}
				{if $it.tiki_p_add_events eq 'y'}
				<option value="{$it.calendarId}" style="background-color:#{$it.custombgcolor};color:#{$it.customfgcolor};"
				{if $calitem.calendarId}
					{if $calitem.calendarId eq $itid} selected="selected"{/if}
				{else}
					{if $calendarView}
						{if $calendarView eq $itid} selected="selected"{/if}
					{else}
						{if $calendarId}
							{if $calendarId eq $itid} selected="selected"{/if}
						{/if}
					{/if}
				{/if}>{$it.name|escape}</option>
				{/if}
			{/foreach}
		</select>
{else}
	{$listcals[$calitem.calendarId].name|escape}
{/if}
	</td>
</tr>

<tr>
<td>{tr}Title{/tr}</td>
<td>
{if $edit}
	<input type="text" name="save[name]" value="{$calitem.name|escape}" size="32" style="width:90%;"/>
{else}
	<span class="summary">{$calitem.name|escape}</span>
{/if}
</td>
</tr>
<tr>
	<td>{tr}Recurrence{/tr}</td>
	<td>
{if $edit}
	{if $recurrence.id gt 0}
	<input type="hidden" name="recurrent" value="1"/>
		{tr}This event depends on a recurrence rule{/tr}
	{else}
<input type="checkbox" id="id_recurrent" name="recurrent" value="1" onclick="toggle('recurrenceRules');toggle('startdate');toggle('enddate')"{if $calitem.recurrenceId gt 0 or $recurrent eq 1}checked="checked"{/if}/><label for="id_recurrent">{tr}This event depends on a recurrence rule{/tr}</label>
	{/if}
{else}
	<span class="summary">{if $calitem.recurrenceId gt 0}{tr}This event depends on a recurrence rule{/tr}{else}{tr}This event is not recurrent{/tr}{/if}</span>
{/if}
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td style="padding:5px 10px">
{if $edit}
	  <div id="recurrenceRules" style="width:100%;{if ( !($calitem.recurrenceId gt 0) and $recurrent neq 1 ) && $prefs.javascript_enabled eq 'y'}display:none;{/if}">
	  {if $calitem.recurrenceId gt 0}<input type="hidden" name="recurrenceId" value="{$recurrence.id}" />{/if}
{if $recurrence.id gt 0}
	{if $recurrence.weekly}
	  <input type="hidden" name="recurrenceType" value="weekly" />{tr}On a weekly basis{/tr}<br />
	{/if}
{else}
	  <input type="radio" id="id_recurrenceTypeW" name="recurrenceType" value="weekly" {if $recurrence.weekly or $calitem.calitemId eq 0}checked="checked"{/if}/><label for="id_recurrenceTypeW">{tr}On a weekly basis{/tr}</label><br />
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
{/if}
{if $recurrence.id eq 0 or $recurrence.weekly}
			  {tr}Each{/tr}&nbsp;
			  <select name="weekday">
				<option value="0" {if $recurrence.weekday eq '0'}selected="selected"{/if}>{tr}Sunday{/tr}</option>
				<option value="1" {if $recurrence.weekday eq '1'}selected="selected"{/if}>{tr}Monday{/tr}</option>
				<option value="2" {if $recurrence.weekday eq '2'}selected="selected"{/if}>{tr}Tuesday{/tr}</option>
				<option value="3" {if $recurrence.weekday eq '3'}selected="selected"{/if}>{tr}Wednesday{/tr}</option>
				<option value="4" {if $recurrence.weekday eq '4'}selected="selected"{/if}>{tr}Thursday{/tr}</option>
				<option value="5" {if $recurrence.weekday eq '5'}selected="selected"{/if}>{tr}Friday{/tr}</option>
				<option value="6" {if $recurrence.weekday eq '6'}selected="selected"{/if}>{tr}Saturday{/tr}</option>
			  </select>
			  &nbsp;{tr}of the week{/tr}
		<br /><hr style="width:75%"/>
{/if}
{if $recurrence.id gt 0}
	{if $recurrence.monthly}
	  <input type="hidden" name="recurrenceType" value="monthly" />{tr}On a monthly basis{/tr}<br />
	{/if}
{else}
		<input type="radio" id="id_recurrenceTypeM" name="recurrenceType" value="monthly" {if $recurrence.monthly}checked="checked"{/if}/><label for="id_recurrenceTypeM">{tr}On a monthly basis{/tr}</label><br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
{/if}
{if $recurrence.id eq 0 or $recurrence.monthly}
			  {tr}Each{/tr}&nbsp;
			  <select name="dayOfMonth">
				{section name=k start=1 loop=32}
				<option value="{$smarty.section.k.index}" {if $recurrence.dayOfMonth eq $smarty.section.k.index}selected="selected"{/if}>{if $smarty.section.k.index lt 10}0{/if}{$smarty.section.k.index}</option>
				{/section}
			  </select>
			  &nbsp;{tr}of the month{/tr}
		<br /><hr style="width:75%"/>
{/if}
{if $recurrence.id gt 0}
	{if $recurrence.yearly}
	  <input type="hidden" name="recurrenceType" value="yearly" />{tr}On a yearly basis{/tr}<br />
	{/if}
{else}
		<input type="radio" id="id_recurrenceTypeY" name="recurrenceType" value="yearly" {if $recurrence.yearly}checked="checked"{/if}/><label for="id_recurrenceTypeY">{tr}On a yearly basis{/tr}</label><br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
{/if}
{if $recurrence.id eq 0 or $recurrence.yearly}
			  {tr}Each{/tr}&nbsp;
			  <select name="dateOfYear_day" onChange="checkDateOfYear(this.options[this.selectedIndex].value,document.forms['f'].elements['dateOfYear_month'].options[document.forms['f'].elements['dateOfYear_month'].selectedIndex].value);">
				{section name=k start=1 loop=32}
				<option value="{$smarty.section.k.index}" {if $recurrence.dateOfYear_day eq $smarty.section.k.index}selected="selected"{/if}>{if $smarty.section.k.index lt 10}0{/if}{$smarty.section.k.index}</option>
				{/section}
			  </select>
			  &nbsp;{tr}of{/tr}&nbsp;
			  <select name="dateOfYear_month" onChange="checkDateOfYear(document.forms['f'].elements['dateOfYear_day'].options[document.forms['f'].elements['dateOfYear_day'].selectedIndex].value,this.options[this.selectedIndex].value);">
				<option value="1"  {if $recurrence.dateOfYear_month eq '1'}selected="selected"{/if}>{tr}January{/tr}</option>
				<option value="2"  {if $recurrence.dateOfYear_month eq '2'}selected="selected"{/if}>{tr}February{/tr}</option>
				<option value="3"  {if $recurrence.dateOfYear_month eq '3'}selected="selected"{/if}>{tr}March{/tr}</option>
				<option value="4"  {if $recurrence.dateOfYear_month eq '4'}selected="selected"{/if}>{tr}April{/tr}</option>
				<option value="5"  {if $recurrence.dateOfYear_month eq '5'}selected="selected"{/if}>{tr}May{/tr}</option>
				<option value="6"  {if $recurrence.dateOfYear_month eq '6'}selected="selected"{/if}>{tr}June{/tr}</option>
				<option value="7"  {if $recurrence.dateOfYear_month eq '7'}selected="selected"{/if}>{tr}July{/tr}</option>
				<option value="8"  {if $recurrence.dateOfYear_month eq '8'}selected="selected"{/if}>{tr}August{/tr}</option>
				<option value="9"  {if $recurrence.dateOfYear_month eq '9'}selected="selected"{/if}>{tr}September{/tr}</option>
				<option value="10" {if $recurrence.dateOfYear_month eq '10'}selected="selected"{/if}>{tr}October{/tr}</option>
				<option value="11" {if $recurrence.dateOfYear_month eq '11'}selected="selected"{/if}>{tr}November{/tr}</option>
				<option value="12" {if $recurrence.dateOfYear_month eq '12'}selected="selected"{/if}>{tr}December{/tr}</option>
			  </select>
&nbsp;&nbsp;
			  <span id="errorDateOfYear" style="color:#900;"></span>
		<br /><br /><hr />
{/if}
		<br />
{if $recurrence.id gt 0}
	<input type="hidden" name="startPeriod" value="{$recurrence.startPeriod}"/>
	<input type="hidden" name="nbRecurrences" value="{$recurrence.nbRecurrences}"/>
	<input type="hidden" name="endPeriod" value="{$recurrence.endPeriod}"/>
	{tr}Starting on{/tr} {$recurrence.startPeriod|tiki_long_date},&nbsp;
	{if $recurrence.endPeriod gt 0}{tr}ending by{/tr} {$recurrence.endPeriod|tiki_long_date}{else}{tr}ending after{/tr} {$recurrence.nbRecurrences} {tr}events{/tr}{/if}.
{else}
		{tr}Start period{/tr} :
		{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
			{jscalendar id="startPeriod" date=$recurrence.startPeriod fieldname="startPeriod" align="Bc" showtime='n'}
		{else}
			{html_select_date prefix="startPeriod_" time=$recurrence.startPeriod field_order=$prefs.display_field_order start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year}
		{/if}
		<br /><hr style="width:75%"/>
		<input type="radio" id="id_endTypeNb" name="endType" value="nb" {if $recurrence.nbRecurrences or $calitem.calitemId eq 0}checked="checked"{/if}/>&nbsp;<label for="id_endTypeNb">{tr}End after{/tr}</label>
		<input type="text" name="nbRecurrences" size="3" style="text-align:right" value="{if $recurrence.nbRecurrences gt 0}{$recurrence.nbRecurrences}{else}{if $calitem.calitemId eq 0}1{/if}{/if}"/>{tr}occurrences{/tr}<br />
		<input type="radio" id="id_endTypeDt" name="endType" value="dt" {if $recurrence.endPeriod gt 0}checked="checked"{/if}/>&nbsp;<label for="id_endTypeDt">{tr}End before{/tr}</label>
		{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
			{jscalendar id="endPeriod" date=$recurrence.endPeriod fieldname="endPeriod" align="Bc" showtime='n'}
		{else}
			{html_select_date prefix="endPeriod_" time=$recurrence.endPeriod field_order=$prefs.display_field_order start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year}
		{/if}
{/if}
		<br />&nbsp;
	  </div>
{else}
	{if $recurrence.id > 0}
		{if $recurrence.weekly}
	  		{tr}Event is repeated{/tr} {if $recurrence.nbRecurrences gt 0}{$recurrence.nbRecurrences} {tr}times{/tr}, {/if}{tr}every{/tr}&nbsp;{tr}{$daysnames[$recurrence.weekday]}{/tr}
		{elseif $recurrence.monthly}
	  		{tr}Event is repeated{/tr} {if $recurrence.nbRecurrences gt 0}{$recurrence.nbRecurrences} {tr}times{/tr}, {/if}{tr}on{/tr}&nbsp;{$recurrence.dayOfMonth} {tr}of every month{/tr}
		{else}
	  		{tr}Event is repeated{/tr} {if $recurrence.nbRecurrences gt 0}{$recurrence.nbRecurrences} {tr}times{/tr}, {/if}{tr}on each{/tr}&nbsp;{$recurrence.dateOfYear_day} {tr}of{/tr} {tr}{$monthnames[$recurrence.dateOfYear_month]}{/tr}
		{/if}
	<br />
	{tr}Starting on{/tr} {$recurrence.startPeriod|tiki_long_date}
	{if $recurrence.endPeriod gt 0}, {tr}ending by{/tr} {$recurrence.endPeriod|tiki_long_date}{/if}.
	{/if}
{/if}
	</td>
</tr>
<tr>
<td>{tr}Start{/tr}</td>
<td>
{if $edit}
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td style="border:0;padding-top:2px;vertical-align:middle">
			{if $prefs.feature_jscalendar neq 'y' or $prefs.javascript_enabled neq 'y'}
				<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex+1);return false;">{icon _id='plus_small' align='left' width='11' height='8'}</a>
			{/if}
			</td>
			<td rowspan="2" style="border:0;padding-top:2px;vertical-align:middle"><div style="display:block" id="startdate">
			{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
				{jscalendar id="start" date=$calitem.start fieldname="save[date_start]" align="Bc" showtime='n'}
			{else}
				{html_select_date prefix="start_date_" time=$calitem.start field_order=$prefs.display_field_order start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year}
			{/if}
			</div></td>
			<td style="border:0;padding-top:2px;vertical-align:middle">
				<span id="starttimehourplus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.start_Hour.selectedIndex=(document.f.start_Hour.selectedIndex+1);return false;">{icon _id='plus_small' align='left' width='11' height='8'}</a></span>
			</td>
			<td rowspan="2" style="border:0;vertical-align:middle" class="html_select_time">
				<span id="starttime" style="display: {if $calitem.allday} none {else} inline {/if}">{html_select_time prefix="start_" display_seconds=false time=$calitem.start minute_interval=$prefs.calendar_timespan hour_minmax=$hour_minmax}</span>
			</td>
			<td style="border:0;padding-top:2px;vertical-align:middle">
				<span id="starttimeminplus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.start_Minute.selectedIndex=(document.f.start_Minute.selectedIndex+1);return false;">{icon _id='plus_small' align='left' width='11' height='8'}</a></span>
			</td>
			<td style="border:0;padding-top:2px;vertical-align:middle;" rowspan="2">
				<label for="alldayid">
				<input type="checkbox" id="alldayid" name="allday" 
					   onclick="toggleSpan('starttimehourplus');
					   			toggleSpan('starttimehourminus');
					   			toggleSpan('starttime');
					   			toggleSpan('starttimeminplus');
					   			toggleSpan('starttimeminminus');
					   			toggleSpan('endtimehourplus');
					   			toggleSpan('endtimehourminus');
					   			toggleSpan('endtime');
					   			toggleSpan('endtimeminplus');
					   			toggleSpan('endtimeminminus');
					   			toggleSpan('durhourplus');
					   			toggleSpan('durhourminus');
					   			toggleSpan('duration');
					   			toggleSpan('duratione');
					   			toggleSpan('durminplus');
					   			toggleSpan('durminminus');"
					   value="true" {if $calitem.allday} checked="checked" {/if} /> {tr}All day{/tr}</label>
			</td>
		</tr>
		<tr>
			<td style="border:0;vertical-align:middle">
			{if $prefs.feature_jscalendar neq 'y' or $prefs.javascript_enabled neq 'y'}
				<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex-1);return false;">{icon _id='minus_small' align='left' width='11' height='8'}</a>
			{/if}
			</td>
			<td style="border:0;vertical-align:middle">
				<span id="starttimehourminus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.start_Hour.selectedIndex=(document.f.start_Hour.selectedIndex-1);return false;">{icon _id='minus_small' align='left' width='11' height='8'}</a>
			</td>
			<td style="border:0;vertical-align:middle">
				<span id="starttimeminminus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.start_Minute.selectedIndex=(document.f.start_Minute.selectedIndex-1);return false;">{icon _id='minus_small' align='left' width='11' height='8'}</a>
			</td>
		</tr>
	</table>
{else}
    {if $calitem.allday}
	    <abbr class="dtstart" title="{$calitem.start|tiki_short_date}">{$calitem.start|tiki_long_date}</abbr>
    {else}
        <abbr class="dtstart" title="{$calitem.start|isodate}">{$calitem.start|tiki_long_datetime}</abbr>
    {/if}
{/if}
</td>
</tr>
<tr>
	<td>{tr}End{/tr}</td><td>
	{if $edit}
		<input type="hidden" name="save[end_or_duration]" value="end" id="end_or_duration" />
		<div id="end_date" style="display:block"> {* the display:block inline style used here is needed to make toggle() function work properly *}
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td style="border:0;padding-top:2px;vertical-align:middle">
			{if $prefs.feature_jscalendar neq 'y' or $prefs.javascript_enabled neq 'y'}
				<span id="endtimehourplus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex+1);return false;">{icon _id='plus_small' align='left' width='11' height='8'}</a></span>
			{/if}
			</td>
			<td rowspan="2" style="border:0;vertical-align:middle"><div style="display:block" id="enddate">
			{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
				{jscalendar id="end" date=$calitem.end fieldname="save[date_end]" align="Bc" showtime='n'}
			{else}
				{html_select_date prefix="end_date_" time=$calitem.end field_order=$prefs.display_field_order  start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year}
			{/if}
			</div></td>
			<td style="border:0;padding-top:2px;vertical-align:middle">
				<span id="endtimehourplus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.end_Hour.selectedIndex=(document.f.end_Hour.selectedIndex+1);return false;">{icon _id='plus_small' align='left' width='11' height='8'}</a></span>
			</td>
			<td rowspan="2" style="border:0;vertical-align:middle" class="html_select_time">
				<span id="endtime" style="display: {if $calitem.allday} none {else} inline {/if}">{html_select_time prefix="end_" display_seconds=false time=$calitem.end minute_interval=$prefs.calendar_timespan hour_minmax=$hour_minmax}</span>
			</td>
			<td style="border:0;padding-top:2px;vertical-align:middle">
				<span id="endtimeminplus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.end_Minute.selectedIndex=(document.f.end_Minute.selectedIndex+1);return false;">{icon _id='plus_small' align='left' width='11' height='8'}</a></span>
			</td>
			<td rowspan="2" style="border:0;padding-top:2px;vertical-align:middle">
				<span id="duration" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.getElementById('end_or_duration').value='duration';flip('end_duration');flip('end_date');return false;return false;">{tr}Duration{/tr}</a></span>
			</td>
		</tr>
		<tr>
		<td style="border:0;vertical-align:middle">
		{if $prefs.feature_jscalendar neq 'y' or $prefs.javascript_enabled neq 'y'}
			<a href="#" onclick="document.f.Time_Hour.selectedIndex=(document.f.Time_Hour.selectedIndex-1);return false;">{icon _id='minus_small' align='left' width='11' height='8'}</a>
		{/if}
		</td>
		<td style="border:0;vertical-align:middle">
			<span id="endtimehourminus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.end_Hour.selectedIndex=(document.f.end_Hour.selectedIndex-1);return false;">{icon _id='minus_small' align='left' width='11' height='8'}</a></span>
		</td>
		<td style="border:0;vertical-align:middle">
			<span id="endtimeminminus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.end_Minute.selectedIndex=(document.f.end_Minute.selectedIndex-1);return false;">{icon _id='minus_small' align='left' width='11' height='8'}</a></span>
		</td>
	</tr>
</table>
</div>

<div id="end_duration" style="display:none;">
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="border:0;padding-top:2px;vertical-align:middle">
			<span id="durhourplus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.duration_Hour.selectedIndex=(document.f.duration_Hour.selectedIndex+1);return false;">{icon _id='plus_small' align='left' width='11' height='8'}</a></span>
		</td>
		<td style="border:0;vertical-align:middle" rowspan="2" class="html_select_time">
			<span id="duratione" style="display: {if $calitem.allday} none {else} inline {/if}">{html_select_time prefix="duration_" display_seconds=false time=$calitem.duration|default:'01:00' minute_interval=$prefs.calendar_timespan}</span>
		</td>
		<td style="border:0;padding-top:2px;vertical-align:middle">
			<span id="durminplus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.duration_Minute.selectedIndex=(document.f.duration_Minute.selectedIndex+1);return false;">{icon _id='plus_small' align='left' width='11' height='8'}</a></span>
		</td>
		<td rowspan="2" style="border:0;padding-top:2px;vertical-align:middle">
			<a href="#" onclick="document.getElementById('end_or_duration').value='end';flip('end_date');flip('end_duration');return false;">{tr}Date and time of end{/tr}</a>
		</td>
	</tr>
	<tr>
		<td style="border:0;vertical-align:middle">
			<span id="durhourminus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.duration_Hour.selectedIndex=(document.f.duration_Hour.selectedIndex-1);return false;">{icon _id='minus_small' align='left' width='11' height='8'}</a></span>
		</td>
		<td style="border:0;vertical-align:middle">
			<span id="durminminus" style="display: {if $calitem.allday} none {else} inline {/if}"><a href="#" onclick="document.f.duration_Minute.selectedIndex=(document.f.duration_Minute.selectedIndex-1);return false;">{icon _id='minus_small' align='left' width='11' height='8'}</a></span>
		</td>
	</tr>
</table>
</div>
{else}
    {if $calitem.allday}
        {if $calitem.end}<abbr class="dtend" title="{$calitem.end|tiki_short_date}">{/if}{$calitem.end|tiki_long_date}{if $calitem.end}</abbr>{/if}
    {else}
        {if $calitem.end}<abbr class="dtend" title="{$calitem.end|isodate}">{/if}{$calitem.end|tiki_long_datetime}{if $calitem.end}</abbr>{/if}
    {/if}
{/if}
{if $impossibleDates}
<br />
<span style="color:#900;">{tr}Events cannot end before they start{/tr}</span>
{/if}
</td>
</tr>
<tr>
<td>{tr}Description{/tr}
</td><td>
{if $edit}
  {textarea name="save[description]" id="editwiki"}{$calitem.description}{/textarea}
{else}
  <span class="description">{$calitem.parsed|default:"<i>{tr}No description{/tr}</i>"}</span>
{/if}
</td></tr>

{if $calendar.customstatus ne 'n'}
<tr><td>{tr}Status{/tr}</td><td>

<div class="statusbox{if $calitem.status eq 0} status0{/if}">
{if $edit}
<input id="status0" type="radio" name="save[status]" value="0"{if (!empty($calitem) and $calitem.status eq 0) or (empty($calitem) and $calendar.defaulteventstatus eq 0)} checked="checked"{/if} />
<label for="status0">{tr}Tentative{/tr}</label>
{else}
{tr}Tentative{/tr}
{/if}
</div>
<div class="statusbox{if $calitem.status eq 1} status1{/if}">
{if $edit}
<input id="status1" type="radio" name="save[status]" value="1"{if $calitem.status eq 1} checked="checked"{/if} />
<label for="status1">{tr}Confirmed{/tr}</label>
{else}
{tr}Confirmed{/tr}
{/if}
</div>
<div class="statusbox{if $calitem.status eq 2} status2{/if}">
{if $edit}
<input id="status2" type="radio" name="save[status]" value="2"{if $calitem.status eq 2} checked="checked"{/if} />
<label for="status2">{tr}Cancelled{/tr}</label>
{else}
{tr}Cancelled{/tr}
{/if}
</div>
</td></tr>
{/if}

{if $calendar.custompriorities eq 'y'}
<tr><td>
{tr}Priority{/tr}</td><td>
{if $edit}
<select name="save[priority]" style="background-color:#{$listprioritycolors[$calitem.priority]};font-size:150%;"
onchange="this.style.bacgroundColor='#'+this.selectedIndex.value;">
{foreach item=it from=$listpriorities}
<option value="{$it}" style="background-color:#{$listprioritycolors[$it]};"{if $calitem.priority eq $it} selected="selected"{/if}>{$it}</option>
{/foreach}
</select>
{else}
<span style="background-color:#{$listprioritycolors[$calitem.priority]};font-size:150%;width:90%;padding:1px 4px">{$calitem.priority}</span>
{/if}

</td></tr>
{/if}
<tr style="display:{if $calendar.customcategories eq 'y'}tablerow{else}none{/if};" id="calcat">
<td>{tr}Classification{/tr}</td>
<td>
{if $edit}
{if count($listcats)}
<select name="save[categoryId]">
<option value=""></option>
{foreach item=it from=$listcats}
<option value="{$it.categoryId}"{if $calitem.categoryId eq $it.categoryId} selected="selected"{/if}>{$it.name|escape}</option>
{/foreach}
</select>
{tr}or new{/tr} {/if}
<input type="text" name="save[newcat]" value="" />
{else}
<span class="category">{$calitem.categoryName|escape}</span>
{/if}
</td>
</tr>
<tr style="display:{if $calendar.customlocations eq 'y'}tablerow{else}none{/if};" id="calloc">
<td>{tr}Location{/tr}</td>
<td>
{if $edit}
{if count($listlocs)}
<select name="save[locationId]">
<option value=""></option>
{foreach item=it from=$listlocs}
<option value="{$it.locationId}"{if $calitem.locationId eq $it.locationId} selected="selected"{/if}>{$it.name|escape}</option>
{/foreach}
</select>
{tr}or new{/tr} {/if}
<input type="text" name="save[newloc]" value="" />
{else}
<span class="location">{$calitem.locationName|escape}</span>
{/if}
</td>
</tr>
<tr>
<td>{tr}URL{/tr}</td>
<td>
{if $edit}
<input type="text" name="save[url]" value="{$calitem.url}" size="32" style="width:90%;" />
{else}
<a class="url" href="{$calitem.url}">{$calitem.url|escape}</a>
{/if}
</td>
</tr>
<tr style="display:{if $calendar.customlanguages eq 'y'}tablerow{else}none{/if};" id="callang">
<td>{tr}Language{/tr}</td>
<td>
{if $edit}
<select name="save[lang]">
<option value=""></option>
{foreach item=it from=$listlanguages}
<option value="{$it.value}"{if $calitem.lang eq $it.value} selected="selected"{/if}>{$it.name}</option>
{/foreach}
</select>
{else}
{$calitem.lang|langname}
{/if}
</td>
</tr>

{if $groupforalert ne ''}
{if $showeachuser eq 'y' }
<tr>
<td>{tr}Choose users to alert{/tr}</td>
<td>
{/if}
{section name=idx loop=$listusertoalert}
{if $showeachuser eq 'n' }
<input type="hidden"  name="listtoalert[]" value="{$listusertoalert[idx].user}">
{else}
<input type="checkbox" name="listtoalert[]" value="{$listusertoalert[idx].user}"> {$listusertoalert[idx].user}
{/if}
{/section}
</td>
</tr>
{/if}


{if $calendar.customparticipants eq 'y'}
	<tr><td colspan="2">&nbsp;</td></tr>
{/if}

<tr style="display:{if $calendar.customparticipants eq 'y'}tablerow{else}none{/if};" id="calorg">
<td>{tr}Organized by{/tr}</td>
<td>
{if $edit}
	{if $preview or $changeCal}
		<input type="text" name="save[organizers]" value="{$calitem.organizers|escape}" style="width:90%;" />
	{else}
		<input type="text" name="save[organizers]" value="{foreach item=org from=$calitem.organizers name=organizers}{if $org neq ''}{$org|escape}{if !$smarty.foreach.organizers.last},{/if}{/if}{/foreach}" style="width:90%;" />
	{/if}
{else}
{foreach item=org from=$calitem.organizers}
{$org|userlink}<br />
{/foreach}
{/if}
</td>
</tr>

<tr style="display:{if $calendar.customparticipants eq 'y'}tablerow{else}none{/if};" id="calpart">
<td>{tr}Participants{/tr}
{if $edit}
<a href="#" onclick="flip('calparthelp');return false;">{icon _id='help'}</a>
{/if}
</td>
<td>
{if $edit}
	{if $preview or $changeCal}
		<input type="text" name="save[participants]" value="{$calitem.participants}" style="width:90%;" />
	{else}
		<input type="text" name="save[participants]" value="{foreach item=ppl from=$calitem.participants name=participants}{if $ppl.name neq ''}{if $ppl.role}{$ppl.role}:{/if}{$ppl.name}{if !$smarty.foreach.participants.last},{/if}{/if}{/foreach}" style="width:90%;" />
	{/if}
{else}
{foreach item=ppl from=$calitem.participants}
{$ppl.name|userlink} {if $listroles[$ppl.role]}({$listroles[$ppl.role]}){/if}<br />
{if $ppl.name eq $user}{assign var='in_particip' value='y'}{/if}
{/foreach}
{if $tiki_p_calendar_add_my_particip eq 'y'}
	{if $in_particip eq 'y'}
		{button _text="{tr}Withdraw me from the list of participants{/tr}" href="?del_me=y&viewcalitemId=$id"}
	{else}
		{button _text="{tr}Add me to the list of participants{/tr}" href="?add_me=y&viewcalitemId=$id"}
	{/if}
{/if}
{if $tiki_p_calendar_add_guest_particip eq 'y'}
	<form action="tiki-calendar_edit_item.php" method="post">
	<input type ="hidden" name="viewcalitemId" value="{$id}" />
	<input type="text" name="guests" />{help desc="{tr}Format{/tr}: {tr}Participant names separated by comma{/tr}" url='calendar'}
	<input type="submit" name="add_guest" value="Add guests" />
	</form>
{/if}
{/if}
</td>
</tr>




<tr><td colspan="2">
{if $edit}
<div style="display:{if $calendar.customparticipants eq 'y' and (isset($cookie.show_calparthelp) and $cookie.show_calparthelp eq 'y')}block{else}none{/if};" id="calparthelp">
{tr}Roles{/tr}<br />
0: {tr}chair{/tr} ({tr}default role{/tr})<br />
1: {tr}required participant{/tr}<br />
2: {tr}optional participant{/tr}<br />
3: {tr}non participant{/tr}<br />
<br />
{tr}Give participant list separated by commas. Roles have to be given in a prefix separated by a column like in:{/tr}
<tt>{tr}role:login_or_email,login_or_email{/tr}</tt>
<br />
{tr}If no role is provided, default role will be "Chair participant".{/tr}
{/if}
</div>

</td></tr>


</table>

{if $edit}
<table class="normal">
{if $recurrence.id gt 0}
<tr><td>
	<input type="radio" id="id_affectEvt" name="affect" value="event" checked="checked"/><label for="id_affectEvt">{tr}Update this event only{/tr}</label><br />
	<input type="radio" id="id_affectMan" name="affect" value="manually"/><label for="id_affectMan">{tr}Update every unchanged events of this recurrence rule{/tr}</label><br />
	<input type="radio" id="id_affectAll" name="affect" value="all"/><label for="id_affectAll">{tr}Update every events of this recurrence rule{/tr}</label>
</td></tr>
{/if}
{if !$user and $prefs.feature_antibot eq 'y'}
	{include file='antibot.tpl'}
{/if}
<tr><td>
	<input type="submit" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;" />
	&nbsp;<input type="submit" name="act" value="{tr}Save{/tr}" onclick="needToConfirm=false;" />
	{if $id}&nbsp;<input type="submit" onclick="needToConfirm=false;{$autosave_js}document.location='tiki-calendar_edit_item.php?calitemId={$id}&amp;delete=y';return false;" value="{tr}Delete event{/tr}" />{/if}
	{if $recurrence.id}&nbsp;<input type="submit" onclick="needToConfirm=false;{$autosave_js}document.location='tiki-calendar_edit_item.php?recurrenceId={$recurrence.id}&amp;delete=y';return false;" value="{tr}Delete Recurrent events{/tr}"/>{/if}
	&nbsp;<input type="submit" onclick="needToConfirm=false;{$autosave_js}document.location='{$referer|escape:'html'}';return false;" value="{tr}Cancel{/tr}" />
</td></tr>
</table>
{/if}
</form>
</div>
{/strip}
