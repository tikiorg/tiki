{* $Id: tiki-tracker_reports.tpl,v 1.1.2.1 2008/07/11 17:44:01 kerrnel22 Exp $ *}
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

{if $tiki_p_admin_trackers eq 'y'}
{* Tracker Select *}
<div name="form">
<form name="tr" action="tiki-tracker_reports.php" method="post">
{if $tr_info.submit=='next' or $tr_info.submit=='submit'}
  <div name="fields">
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

  <div name="submit">
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

{else}
  <div name="gettracker">
    <b>Select Tracker for Reporting:</b><br />
    &nbsp;&nbsp;<select size="{if $tr_info|@count < 5}{$tr_info|@count}{else}5{/if}" name="trackers">
     {section name=trk loop=$tr_info}
      <option value="{$tr_info[trk].trackerId}" title="{$tr_info[trk].description}"{if $trackerId and $tr_info[trk].trackerId == $trackerId} selected{elseif $smarty.section.trk.index == 0 and !$trackerId} selected{/if}>{$tr_info[trk].name}</option>
     {/section}
    </select>
  </div>

  <div name="daterange">
    <p>
      <b>Select a Date Range:</b><br />
      <i>If you specify a custom date range, you must select CUSTOM.  Default is month-to-date.</i>
      <table>
        <tr>
          <td colspan="4">
            <input type="radio" name="range" value="all" checked>Entire History<br />
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="range" value="ytd">Year-to-Date
          </td>
          <td>
            <input type="radio" name="range" value="qtd">This Quarter
          </td>
          <td>
            <input type="radio" name="range" value="mtd" CHECKED>This Month
          </td>
          <td>
            <input type="radio" name="range" value="wtd">This Week
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="range" value="ly">Last Year
          </td>
          <td>
            <input type="radio" name="range" value="lq">Last Quarter
          </td>
          <td>
            <input type="radio" name="range" value="lm">Last Month
          </td>
          <td>
            <input type="radio" name="range" value="lw">Last Week
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="range" value="custom">Custom:<br />
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

{* Ignore this section for now.
  <div name="setgranularity">
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

  <div name="destination">
    <p>
      <b>Report Destination:</b><br />
      &nbsp;&nbsp;<select name="format">
        <option value="c" selected>Downloadable CSV</option>
        <option value="s" DISABLED>Screen</option>
        <option value="w" DISABLED>Wiki Page (not ready)</option>
      </select>
    </p>
  </div>
  <div name="submit">
    <p>
      <button name="submit" value="cancel">Cancel</button>
      <button name="submit" value="new">New Report</button>
      <button name="submit" value="submit">Next &gt;</button>
    </p>
  </div>
{/if}

</form>
</div>

{else}

<b>You do not have access to Tracker Reports.</b>

{/if}
