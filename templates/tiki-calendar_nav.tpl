{* calendar view to be included in tiki-calendar.tpl, tiki-action_calendar.tpl, and wikiplugin-calendar *}
<div class="tabrow">

<div class="viewmode">
<a href="{$myurl}?viewmode=day" class="viewmode{if $viewmode eq 'day'}on{else}off{/if}" title="{tr}day{/tr}"><img src="img/icons/cal_day.gif" width="30" height="24" border="0" alt="{tr}day{/tr}" /></a>
<a href="{$myurl}?viewmode=week" class="viewmode{if $viewmode eq 'week'}on{else}off{/if}" title="{tr}week{/tr}"><img src="img/icons/cal_week.gif" width="30" height="24" border="0" alt="{tr}week{/tr}" /></a>
<a href="{$myurl}?viewmode=month" class="viewmode{if $viewmode eq 'month'}on{else}off{/if}" title="{tr}month{/tr}"><img src="img/icons/cal_month.gif" width="30" height="24" border="0" alt="{tr}month{/tr}" /></a>
<a href="{$myurl}?viewmode=quarter" class="viewmode{if $viewmode eq 'quarter'}on{else}off{/if}" title="{tr}quarter{/tr}"><img src="img/icons/cal_quarter.gif" width="30" height="24" border="0" alt="{tr}quarter{/tr}" /></a>
<a href="{$myurl}?viewmode=semester" class="viewmode{if $viewmode eq 'semester'}on{else}off{/if}" title="{tr}semester{/tr}"><img src="img/icons/cal_semester.gif" width="30" height="24" border="0" alt="{tr}semester{/tr}" /></a>
<a href="{$myurl}?viewmode=year" class="viewmode{if $viewmode eq 'year'}on{else}off{/if}" title="{tr}year{/tr}"><img src="img/icons/cal_year.gif" width="30" height="24" border="0" alt="{tr}year{/tr}" /></a>
</div>

{if $prefs.feature_jscalendar eq 'y'}
<div class="jscalrow">
<form action="{$myurl}" method="post" name="f">
{jscalendar date="$focusdate" id="trig" goto="$jscal_url" align="Bc"}
</form>
</div>
{else}
<div class="daterow">
<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td><a href="{$myurl}?todate={$daybefore}" class="link" title="{$daybefore|tiki_long_date}">&laquo;&nbsp;{tr}day{/tr}</a></td>
<td style="text-align:center; padding-right:5px; padding-left: 5px;" class="middle" rowspan="3" nowrap="nowrap"><b>{tr}Focus:{/tr}<br />{$focusdate|tiki_long_date}</b></td>
<td><a href="{$myurl}?todate={$dayafter}" class="link" title="{$dayafter|tiki_long_date}">{tr}day{/tr}&nbsp;&raquo;</a></td></tr>
<tr>
<td><a href="{$myurl}?todate={$weekbefore}" class="link" title="{$weekbefore|tiki_long_date}">&laquo;&nbsp;{tr}week{/tr}</a></td>
<td><a href="{$myurl}?todate={$weekafter}" class="link" title="{$weekafter|tiki_long_date}">{tr}week{/tr}&nbsp;&raquo;</a></td></tr>
<tr>
<td><a href="{$myurl}?todate={$monthbefore}" class="link" title="{$monthbefore|tiki_long_date}">&laquo;&nbsp;{tr}month{/tr}</a></td>
<td><a href="{$myurl}?todate={$monthafter}" class="link" title="{$monthafter|tiki_long_date}">{tr}month{/tr}&nbsp;&raquo;</a></td></tr>
</table>
</div>
{/if}

<div class="calnavigation">
{if $viewmode eq "day"}
<a href="{$myurl}?todate={$daybefore}" title="&laquo; {tr}day{/tr}" class="prev">&laquo;</a>
<a href="{$myurl}?todate={$dayafter}" title="{tr}day{/tr} &raquo;" class="next">&raquo;</a>
{$viewstart|tiki_date_format:"%B %e"}
{elseif $viewmode eq "week"}
<a href="{$myurl}?todate={$weekbefore}" title="&laquo; {tr}week{/tr}" class="prev">&laquo;</a>
<a href="{$myurl}?todate={$weekafter}" title="{tr}week{/tr} &raquo;" class="next">&raquo;</a>
{$viewstart|tiki_date_format:"%B %e"} - {$viewend|tiki_date_format:"%B %e"}
{elseif $viewmode eq "month"}
<a href="{$myurl}?todate={$monthbefore}" title="&laquo; {tr}month{/tr}" class="prev">&laquo;</a>
<a href="{$myurl}?todate={$monthafter}" title="{tr}month{/tr}&raquo;" class="next">&raquo;</a>
{$daystart|tiki_date_format:"%B"}
{elseif $viewmode eq "quarter"}
<a href="{$myurl}?todate={$quarterbefore}" title="&laquo; {tr}quarter{/tr}" class="prev">&laquo;</a>
<a href="{$myurl}?todate={$quarterafter}" title="{tr}quarter{/tr} &raquo;" class="next">&raquo;</a>
{$daystart|tiki_date_format:"%B %e"} - {$dayend|tiki_date_format:"%B %e"}
{elseif $viewmode eq "semester"}
<a href="{$myurl}?todate={$semesterbefore}" title="&laquo; {tr}semester{/tr}" class="prev">&laquo;</a>
<a href="{$myurl}?todate={$semesterafter}" title="{tr}semester{/tr} &raquo;" class="next">&raquo;</a>
{$daystart|tiki_date_format:"%B %e"} - {$dayend|tiki_date_format:"%B %e"}
{elseif $viewmode eq "year"}
<a href="{$myurl}?todate={$yearbefore}" title="&laquo; {tr}year{/tr}" class="prev">&laquo;</a>
<a href="{$myurl}?todate={$yearafter}" title="{tr}year{/tr} &raquo;" class="next">&raquo;</a>
{$daystart|tiki_date_format:"%Y"}
{/if}
</div>

</div>
