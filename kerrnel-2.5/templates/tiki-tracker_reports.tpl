{* $Id$ *}
<h1>
  {if $tr_info.scope}
    <a class="pagetitle" href="tiki-tracker_reports.php?scope={$tr_info.scope}">{tr}Tracker report:{/tr} {$tr_info.scopename}</a>
  {else}
    <a class="pagetitle" href="tiki-tracker_reports.php">{tr}Tracker reports{/tr}</a>
  {/if}
</h1>

{* Menu *}
<div>
  {if (isset($tiki_p_list_trackers) and $tiki_p_list_trackers eq 'y' or (!isset($tiki_p_list_trackers) and $tiki_p_view_trackers eq 'y'))}<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>{/if}
  {if $tiki_p_view_trackers eq 'y' and $trackerId}<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}" class="linkbut">{tr}View this tracker items{/tr}</a></span>{/if}
  {if $tiki_p_admin_trackers eq 'y'}&nbsp;<span class="button2"><a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a></span>
  {if $trackerId}<span class="button2"><a href="tiki-admin_trackers.php?trackerId={$trackerId}" class="linkbut">{tr}Edit this tracker{/tr}</a></span> <span class="button2"><a href="tiki-admin_tracker_fields.php?trackerId={$trackerId}" class="linkbut">{tr}Edit fields{/tr}</a></span>{/if}{/if}
</div>
<br />

{* Tracker Select *}
<div id="form">
<form name="tr" action="tiki-tracker_reports.php" method="post">
{if $tr_info.submit=='next' or $tr_info.submit=='submit'}
  <div id="fields">
    {assign var=tr_fields value=$tr_info.fields}
    {section name=ix loop=$tr_fields.list}
      <table>
        <tr>
          <td>
            <input type="checkbox" name="moo"> {$tr_fields.list[ix].name}
	      </td>
        </tr>
      </table>
    {/section}
    <p />
    {if $tr_info.format != 'c'}
      <i><b>This report option is not yet supported.</b></i><p />
      Trackers: {$tr_info.trackers}<br />
      Range: {$tr_info.range}<br />
      Granularity: {$tr_info.granularity}<br />
      Format: {$tr_info.format}<br />
    {else}
      <i><b>Downloadable CSV File</b></i><p />
    {/if}
  </div>

  <div id="submit">
    <p>
      <input type="hidden" name="trackers" value="{$tr_info.trackers}">
      <input type="hidden" name="range" value="{$tr_info.range}">
      <input type="hidden" name="custom_from" value="{$tr_info.custom_from}">
      <input type="hidden" name="custom_to" value="{$tr_info.custom_to}">
      <input type="hidden" name="granularity" value="{$tr_info.granularity}">
      <input type="hidden" name="format" value="{$tr_info.format}">
      <button name="submit" value="cancel">Cancel</button>
      <button name="submit" value="new">New Report</button>
      {*<button name="submit" value="submit">Generate Report</button>*}
    </p>
  </div>

{else}	{**************** NEW FORM *****************}
  <div id="destination">
      <h2>Report Destination:</h2><br />
      &nbsp;&nbsp;<select name="format" onchange="javascript:hide('c');hide('s');hide('w');show(this.options[selectedIndex].value);">
        <option value="c" SELECTED>Downloadable CSV</option>
        <option value="s" DISABLED>Screen</option>
        <option value="w" DISABLED>Wiki Page</option>
      </select>
  </div>
  <br />
  <div id="options" class="clearfix" style="margin-left: 15px;">
    <span class="button2" style="top-margin=4x;">
      <a class="linkbut">{tr}Options{/tr}</a>
    </span>
    <div id="options-box" class="wiki-edithelp" style="width:50%;">
      <div id="c">
        Delimiter:
          <input type="radio" name="delimiter" style="margin-left:10px;" value="," CHECKED> ,
          <input type="radio" name="delimiter" style="margin-left:10px;" value=";"> ;
          <input type="radio" name="delimiter" style="margin-left:10px;" value="|"> |
          <input type="radio" name="delimiter" style="margin-left:10px;" value="tab"> [tab]
          <input type="radio" name="delimiter" style="margin-left:10px;" value=" "> [space]
          <input type="radio" name="delimiter" style="margin-left:10px;" value="o"> other:
          <input type="text" name="otherdel" size="1" maxlength="1">
          <br />
        Text Field Wrapper:
          <select name="textqual">
            <option value="double" SELECTED>"</option>
            <option value="single">'</option>
            <option value="none">[none]</option>
          </select>
          <br />
        Carriage Return (&lt;CR&gt;) Appears Inside Field as:
          <input type="text" name="fieldcr" size="4" maxlength="4" value="%%%">
          <br />
        Parse as Wiki Text?
          <input type="checkbox" name="parse">
      </div>
      <div id="s" style="display:none;">
        Screen
      </div>
      <div id="w" style="display:none;">
        Wiki
      </div>
    </div>
  </div>
  <br />
  <div id="gettracker">
    <h2>Select Tracker to Report on:</h2><br />
    &nbsp;&nbsp;
    <select size="{if $tr_info|@count < 5}{$tr_info|@count}{else}5{/if}" 
            name="trackers" 
            onchange="javascript:{foreach key=trk item=tri from=$tr_info}hide('k'+'{$tri.trackerId}');{/foreach}{foreach key=trk item=tri from=$tr_info}hide('f'+'{$tri.trackerId}');{/foreach}show('k'+this.options[selectedIndex].value); show('f'+this.options[selectedIndex].value);">
      {section name=trk loop=$tr_info}
        <option value="{$tr_info[trk].trackerId}" title="{$tr_info[trk].description|escape}"{if $trackerId and $tr_info[trk].trackerId == $trackerId} selected{elseif $smarty.section.trk.index == 0 and !$trackerId} selected{/if}>
           {$tr_info[trk].name}
       </option>
      {/section}
    </select>
  </div>
  <div id="daterange">
    <p>
      <h2>Select a Date Range:</h2><br />
      <i>If you specify a custom date range, you must select CUSTOM.  Default is month-to-date.</i>
      <table>
        <tr>
          <td colspan="4">
            <input type="radio" name="range" value="all" checked> Entire History<br />
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="range" value="ytd"> Year-to-Date
          </td>
          <td>
            <input type="radio" name="range" value="qtd"> This Quarter
          </td>
          <td>
            <input type="radio" name="range" value="mtd" CHECKED> This Month
          </td>
          <td>
            <input type="radio" name="range" value="wtd"> This Week
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="range" value="ly"> Last Year
          </td>
          <td>
            <input type="radio" name="range" value="lq"> Last Quarter
          </td>
          <td>
            <input type="radio" name="range" value="lm"> Last Month
          </td>
          <td>
            <input type="radio" name="range" value="lw"> Last Week
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="range" value="custom"> Custom:<br />
          </td>
          <td align="right">
            From:<br />
            To:
          </td>
          <td colspan="2">
            {assign var=datefrom value=$smarty.now-86400}
            {html_select_date prefix="custom_from" start_year="2007" time=$datefrom}<br />
            {html_select_date prefix="custom_to" start_year="2007" time=$smarty.now}
          </td>
        </tr>
      </table>
    </p>
  </div>

  <div id="datekey" style="display:block;">
    <h3>Select Key Field for Date Range:</h3>
    {foreach key=trk item=tki from=$tr_info}
      <div id="k{$tki.trackerId}" style="display:{if $trk > 0}none{else}block{/if};">
        &nbsp;&nbsp;
        <select size="1" name="datekey">
          {assign var=tf value=$tki.dkfields}
          {section name=tfi loop=$tf}
              <option value="{$tki.trackerId}:{$tf[tfi].fieldId}" title="{$tf[tfi].name}">{$tf[tfi].name}</option>
          {/section}
        </select>
      </div>
    {/foreach}
  </div>

  <div id="fieldlist">
    <p>
      <h2>Select Fields:</h2><br />
      <i>Use the AND/OR button to use pre-set list and augment it, or completely customize it.<br />
      If OR is selected along with any field in the list, then any pre-set selection is over-ridden.</i><br />
      <br />
      &nbsp;&nbsp;<input type="radio" name="canfields" value="vi"> Fields visible in item list<br />
      &nbsp;&nbsp;<input type="radio" name="canfields" value="sl"> Fields searchable or visible in item list<br />
      &nbsp;&nbsp;<input type="radio" name="canfields" value="ef"> All data fields in an item (ignores headings)<br />
      &nbsp;&nbsp;<input type="radio" name="canfields" value="af" CHECKED> All Fields<br />
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="fieldsao" value="and"> <i>AND</i>
      <input type="radio" name="fieldsao" value="or" CHECKED> <i>OR</i> any of the following fields:<br />
      {foreach key=trk item=tki from=$tr_info}
        <div id="f{$tki.trackerId}" style="display:{if $trk > 0}none{else}block{/if};">
          &nbsp;&nbsp;
          <select size="10" name="fields[]" multiple="multiple">
            {assign var=tf value=$tki.fields}
            {section name=tfi loop=$tf}
              {if ($tf[tfi].fieldId == -4 and ($tki.showCreatedView eq 'y' or $tki.showCreated eq 'y')) or
                  ($tf[tfi].fieldId == -3 and ($tki.showLastModifView eq 'y' or $tki.showLastModif eq 'y')) or
                  ($tf[tfi].fieldId == -2 and $tki.showId eq 'y') or
                  ($tf[tfi].fieldId == -1 and $tki.showStatus eq 'y') or
                  $tf[tfi].fieldId > 0}
                <option value="{$tki.trackerId}:{$tf[tfi].fieldId}" title="{$tf[tfi].name}">{$tf[tfi].name}</option>
              {/if}
            {/section}
          </select>
        </div>
      {/foreach}
    </p>
  </div>

{* Ignore this section for now.  Maybe put it in Screen or Wiki option.
  <div id="setgranularity">
    <p>
      <b>Report Granularity:</b><br />
      <i>(smallest time unit displayed; daily will break out by day)</i><br />
      <select name="granularity">
        <option value="q">Quarterly</option>
        <option value="m">Monthly</option>
        <option value="w">Weekly</option>
        <option value="d" selected>Daily</option>
      </select>
    </p>
  </div>
*}

  <div id="submit">
    <p>
      <button name="submit" value="cancel">Cancel</button>
      <button name="submit" value="new">New Report</button>
      <button name="submit" value="submit">Next &gt;</button>
    </p>
  </div>
{/if}

</form>
</div>

