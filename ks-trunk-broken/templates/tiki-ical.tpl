<h2>{tr}Ical import / Export{/tr}</h2>
<hr />
<h3>{tr}Select The calendar to export{/tr}</h3>
<form class="forms" method="get" action="tiki-ical.php">
    <input  name="export" type="hidden"  value="y" />
    <select name="calendarId">
    	{foreach item=lc from=$calendars}
		<option value="{$lc.calendarId}">{$lc.name}</option>
	{/foreach}
    </select>
   <br />{tr}From:{/tr}  
   <input type="hidden" name="tstart" id="tstart" value="{$tstart|escape}" />
   <span id="tstartl" class="daterow" style="padding:0; margin:0">Click</span>
{jq}
   {literal}
	Calendar.setup({
		inputField     :    "tstart",     // id of the input field
		ifFormat       :    "%s",     // format of the input field (even if hidden, this format will be honored)
		displayArea    :    "tstartl",       // ID of the span where the date is to be shown
		daFormat       :    "%d/%m/%y",// format of the displayed date
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true
	});
    {/literal}
{/jq}
    <br /> {tr}To{/tr} :
    <input type="hidden" name="tstop" id="tstop" value="{$tstop|escape}" />
    <span id="tstopl" class="daterow" style="padding:0; margin:0">Click</span>
{jq}
    {literal}
	Calendar.setup({
		inputField     :    "tstop",     // id of the input field
		ifFormat       :    "%s",     // format of the input field (even if hidden, this format will be honored)
		displayArea    :    "tstopl",       // ID of the span where the date is to be shown
		daFormat       :    "%d/%m/%y",// format of the displayed date
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true
	});
    {/literal}
{/jq}
    <br />
   <input  class="wikiaction"  value="export" type="submit" />
</form>
    <br /><br />


<hr />
<h3>{tr}Calendar Import{/tr}</h3>
{if $updated}
{tr}Calendar has been updated{/tr}
{/if}
<form method="post" action="tiki-ical.php" enctype="multipart/form-data">
<table class="formcolor">
<tr><td>{tr}Calendar{/tr}</td><td>
<select name="calendarId">
{foreach item=lc from=$calendars}
<option value="{$lc.calendarId}">{$lc.name|escape}</option>
{/foreach}
</select>
</td></tr>
<tr><td>{tr}ICal File{/tr}
</td><td>
<input type="file" name="fileICS" size="50" />
<input type="submit" name="import" value="{tr}import{/tr}" />
</td></tr></table>
</form>
<p>{$filedata}</p>
<hr />
<p>{$iCal}</p>
<hr />

