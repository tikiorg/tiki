{strip}
{title}{tr}Advanced parameters for iCal export{/tr}{/title}

{if $tiki_p_view_calendar eq 'y'}
	<div class="navbar">
		{button  href="tiki-calendar.php" _text="{tr}Calendar{/tr}"}
	</div>
{/if}

<div class="wikitext">

<form action="tiki-calendar_export_ical.php" method="post" name="f" id="editcalitem">
<input type="hidden" name="export" value="y"/>
<table class="formcolor">
  <tr><td colspan="2">{tr}Calendars to be exported{/tr}</td></tr>
  <tr>
    <td>{tr}Calendars{/tr}</td>
    <td>
{foreach item=k from=$listcals}
	  <input type="checkbox" name="calendarIds[]" value="{$k.calendarId|escape}" id="groupcal_{$k}" {if $k}checked="checked"{/if} />
	  <label for="groupcal_{$k}" class="calId{$k}">{$k.name|escape}</label><br />
{/foreach}
	</td>
  </tr>
  <tr>
    <td>{tr}Start{/tr}</td>
	<td>
	  <input type="hidden" name="tstart" />
      <table cellpadding="0" cellspacing="0" border="0">
	    <tr>
		  <td style="border:0;padding-top:2px;vertical-align:middle">
			{if $prefs.feature_jscalendar neq 'y' or $prefs.javascript_enabled neq 'y'}
			<a href="#" onclick="document.f.tstart.selectedIndex=(document.f.tstart.selectedIndex+1);">{icon _id='plus_small' align='left' width='11' height='8'}</a>
			{/if}
		  </td>
		  <td style="border:0;padding-top:2px;vertical-align:middle">
			{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
				{jscalendar id="start" date=$startTime fieldname="tstart" align="Bc" showtime='n'}
			{else}
				{html_select_date prefix="start_date_" time=$startTime field_order=$prefs.display_field_order start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year}
			{/if}
		  </td>
		</tr>
  	  </table>
    </td>
  </tr>
  <tr>
	<td>{tr}End{/tr}</td>
		<td>
	      <table cellpadding="0" cellspacing="0" border="0">
		    <tr>
			  <td style="border:0;padding-top:2px;vertical-align:middle">
				{if $prefs.feature_jscalendar neq 'y' or $prefs.javascript_enabled neq 'y'}
				<a href="#" onclick="document.f.tstop.selectedIndex=(document.f.tstop.selectedIndex+1);">{icon _id='plus_small' align='left' width='11' height='8'}</a>
				{/if}
			  </td>
			  <td style="border:0;padding-top:2px;vertical-align:middle">
				{if $prefs.feature_jscalendar eq 'y' and $prefs.javascript_enabled eq 'y'}
					{jscalendar id="end" date=$stopTime fieldname="tstop" align="Bc" showtime='n'}
				{else}
					{html_select_date prefix="stop_date_" time=$stopTime field_order=$prefs.display_field_order start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year}
				{/if}
			  </td>
			</tr>
	  	  </table>
</td>
</tr>
<tr>
	<td><input type="submit" name="ical" value="{tr}Export calendars iCal{/tr}"/></td>
	<td><input type="submit" name="csv" value="{tr}Export calendars CSV{/tr}"/></td>
</tr>
</table>
</form>
</div>
{/strip}
