

{* calendar view to be included both in tiki-calendar.tpl and wikiplugin-calendar *}
<div class="tabrow">
<table cellpadding="0" cellspacing="0" border="0">
<tr><td class="middle" nowrap="nowrap" rowspan="2">
{if $prefs.feature_jscalendar eq 'y'}
<form action="tiki-calendar.php" method="post" name="f">
<span title="{tr}Date Selector{/tr}" id="datrigger" class="daterow" >{$focusdate|tiki_long_date}</span>
<span class="date">&lt;- {tr}Click to Navigate{/tr}</span>
<input type="hidden" name="todateit" id="todate" value="" />
</form>
<script type="text/javascript">
{literal}function gotocal()  { {/literal}
window.location = 'tiki-calendar.php?todate='+document.getElementById('todate').value+'{if $calendarId}&calendarId={$calendarId}&editmode=add{/if}';
{literal} } {/literal}
{literal}Calendar.setup( { {/literal}
date        : "{$focusdate|date_format:"%B %e, %Y %H:%M"}",      // initial date
inputField  : "todate",      // ID of the input field
ifFormat    : "%s",    // the date format
displayArea : "datrigger",       // ID of the span where the date is to be shown
daFormat    : "{$daformat2}",  // format of the displayed date
singleClick : true,
onUpdate     : gotocal,
firstDay : {$firstDayofWeek}
{literal} } );{/literal}
</script>
{else}
<div class="daterow">
<table cellspacing="0" cellpadding="0" border="0">
<tr><td><a href="tiki-calendar.php?todate={$daybefore}" class="link" title="{$daybefore|tiki_long_date}">&laquo;&nbsp;{tr}Day{/tr}</a></td><td style="text-align:center; padding-right:5px; padding-left: 5px;" class="middle" rowspan="3" nowrap="nowrap"><b>{tr}Focus:{/tr}<br />{$focusdate|tiki_long_date}</b></td><td><a href="tiki-calendar.php?todate={$dayafter}" class="link" title="{$dayafter|tiki_long_date}">{tr}Day{/tr}&nbsp;&raquo;</a></td></tr>
<tr><td><a href="tiki-calendar.php?todate={$weekbefore}" class="link" title="{$weekbefore|tiki_long_date}">&laquo;&nbsp;{tr}Week{/tr}</a></td><td><a href="tiki-calendar.php?todate={$weekafter}" class="link" title="{$weekafter|tiki_long_date}">{tr}Week{/tr}&nbsp;&raquo;</a></td></tr>
<tr><td><a href="tiki-calendar.php?todate={$monthbefore}" class="link" title="{$monthbefore|tiki_long_date}">&laquo;&nbsp;{tr}Month{/tr}</a></td><td><a href="tiki-calendar.php?todate={$monthafter}" class="link" title="{$monthafter|tiki_long_date}">{tr}Month{/tr}&nbsp;&raquo;</a></td></tr>
</table>
{*<a href="tiki-calendar.php?todate={$monthbefore}" class="link" title="{$monthbefore|tiki_long_date}">{tr}-1m{/tr}</a>
<a href="tiki-calendar.php?todate={$weekbefore}" class="link" title="{$weekbefore|tiki_long_date}">{tr}-7d{/tr}</a>
<a href="tiki-calendar.php?todate={$daybefore}" class="link" title="{$daybefore|tiki_long_date}">{tr}-1d{/tr}</a> 
<b>{$focusdate|tiki_long_date}</b>
<a href="tiki-calendar.php?todate={$dayafter}" class="link" title="{$dayafter|tiki_long_date}">{tr}+1d{/tr}</a>
<a href="tiki-calendar.php?todate={$weekafter}" class="link" title="{$weekafter|tiki_long_date}">{tr}+7d{/tr}</a>
<a href="tiki-calendar.php?todate={$monthafter}" class="link" title="{$monthafter|tiki_long_date}">{tr}+1m{/tr}</a>*}
</div>
{/if}
</td>
<td style="text-align:center;" width="100%" class="middle" rowspan="2">
<div><a href="tiki-calendar.php?todate={$nowUser}" class="linkmodule" title="{tr}Change Focus{/tr}"><b>{tr}Today{/tr}:<br /></b> {$now|tiki_short_datetime}</a></div>
</td>
<td style="text-align:right;" align="right" nowrap="nowrap">
<a href="tiki-calendar.php?viewmode=day{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'day'}on{else}off{/if}" title="{tr}Day{/tr}"><img src="img/icons/cal_day.gif" width="30" height="24" border="0" alt="{tr}Day{/tr}" align="top" /></a>
<a href="tiki-calendar.php?viewmode=week{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'week'}on{else}off{/if}" title="{tr}Week{/tr}"><img src="img/icons/cal_week.gif" width="30" height="24" border="0" alt="{tr}Week{/tr}" align="top" /></a>
<a href="tiki-calendar.php?viewmode=month{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'month'}on{else}off{/if}" title="{tr}Month{/tr}"><img src="img/icons/cal_month.gif" width="30" height="24" border="0" alt="{tr}Month{/tr}" align="top" /></a><br />
<a href="tiki-calendar.php?viewmode=quarter{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'quarter'}on{else}off{/if}" title="{tr}Quarter{/tr}"><img src="img/icons/cal_quarter.gif" width="30" height="24" border="0" alt="{tr}Quarter{/tr}" align="top" /></a>
<a href="tiki-calendar.php?viewmode=semester{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'semester'}on{else}off{/if}" title="{tr}Semester{/tr}"><img src="img/icons/cal_semester.gif" width="30" height="24" border="0" alt="{tr}Semester{/tr}" align="top" /></a>
<a href="tiki-calendar.php?viewmode=year{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'year'}on{else}off{/if}" title="{tr}Year{/tr}"><img src="img/icons/cal_year.gif" width="30" height="24" border="0" alt="{tr}Year{/tr}" align="top" /></a><br /></td></tr>
<tr><td style="text-align:center; padding-top:5px; padding-bottom: 5px">{if $viewlist eq 'list'}<a href="tiki-calendar.php?viewlist=table{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="linkbut" title="{tr}Calendar View{/tr}">{tr}Calendar View{/tr}</a>{else}<a href="tiki-calendar.php?viewlist=list" class="linkbut" title="{tr}List View{/tr}">{tr}List View{/tr}</a>{/if}
</td></tr>

<tr><td colspan="5" class="calnavigation">
{if $viewmode eq "day"}
{assign var="dBefore" value=$viewday-1}
{assign var="dAfter" value=$viewday+1}
<a href="tiki-calendar.php?mon={$viewmonth}&amp;day={$dBefore}&amp;year={$viewyear}" title="&laquo; {tr}Day{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$viewmonth}&amp;day={$dAfter}&amp;year={$viewyear}" title="{tr}Day{/tr} &raquo;">&raquo;</a>&nbsp;{$viewstart|tiki_date_format:"%B %e"}
{/if}
{if $viewmode eq "week"}
{assign var="dBefore" value=$viewday-7}
{assign var="dAfter" value=$viewday+7}
<a href="tiki-calendar.php?mon={$viewmonth}&amp;day={$dBefore}&amp;year={$viewyear}" title="&laquo; {tr}Week{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$viewmonth}&amp;day={$dAfter}&amp;year={$viewyear}" title="{tr}Week{/tr} &raquo;">&raquo;</a>&nbsp;{$viewstart|tiki_date_format:"%B %e"} - {$viewend|tiki_date_format:"%B %e"}
{/if}
{if $viewmode eq "month"}
{assign var="mBefore" value=$viewmonth-1}
{assign var="mAfter" value=$viewmonth+1}
<a href="tiki-calendar.php?mon={$mBefore}&amp;day=1&amp;year={$viewyear}" title="&laquo; {tr}Month{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$mAfter}&amp;day=1&amp;year={$viewyear}" title="{tr}Month{/tr}&raquo;">&raquo;</a>&nbsp;{$daystart|tiki_date_format:"%B %Y"}
{/if}
{if $viewmode eq "quarter"}
{assign var="mBefore" value=$viewmonth-3}
{assign var="mAfter" value=$viewmonth+3}
<a href="tiki-calendar.php?mon={$mBefore}&amp;day=1&amp;year={$viewyear}" title="&laquo; {tr}Quarter{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$mAfter}&amp;day=1&amp;year={$viewyear}" title="{tr}Quarter{/tr} &raquo;">&raquo;</a>&nbsp;{$daystart|tiki_date_format:"%B %Y"} - {$dayend|tiki_date_format:"%B %Y"}
{/if}
{if $viewmode eq "semester"}
{assign var="mBefore" value=$viewmonth-6}
{assign var="mAfter" value=$viewmonth+7}
<a href="tiki-calendar.php?mon={$mBefore}&amp;day=1&amp;year={$viewyear}" title="&laquo; {tr}Semester{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$mAfter}&amp;day=1&amp;year={$viewyear}" title="{tr}Semester{/tr} &raquo;">&raquo;</a>&nbsp;{$daystart|tiki_date_format:"%B %Y"} - {$dayend|tiki_date_format:"%B %Y"}
{/if}
{if $viewmode eq "year"}
{assign var="yBefore" value=$viewyear-1}
{assign var="yAfter" value=$viewyear+1}
<a href="tiki-calendar.php?mon={$viewmonth}&amp;day=1&amp;year={$yBefore}" title="&laquo; {tr}Year{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$viewmonth}&amp;day=1&amp;year={$yAfter}" title="{tr}Year{/tr} &raquo;">&raquo;</a>&nbsp;{$daystart|tiki_date_format:"%Y"}
{/if}
</td></tr>
</table>

{*-------------------------------------------*}
