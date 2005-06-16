{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-calendar.tpl,v 1.54 2005-06-16 20:11:06 mose Exp $ *}
{popup_init src="lib/overlib.js"}

<h1><a class="pagetitle" href="tiki-calendar.php">{tr}Calendar{/tr}</a></h1>
{if $tiki_p_admin_calendar eq 'y' or $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-admin_calendars.php" class="linkbut">{tr}admin{/tr}</a></span>
{/if}
{if $feature_tabs ne 'y'}
<span class="button2"><a href="#filter" class="linkbut">{tr}filter{/tr}</a></span>
{if $modifTab or $modifTab eq "0"}
<span class="button2"><a href="#add"class="linkbut">{tr}add item{/tr}</a></span>
{/if}
{/if}
<br /><br />

{if $feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3" print=false advance=false}
<div id="page-bar">
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Calendar{/tr}</a></span>
{if $tiki_p_view_tiki_calendar eq "y" or $listcals|@count > 1}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Filter{/tr}</a></span>
{else}<span id="tab{cycle name=tabs}" />{/if}
{if $modifTab or $modifTab eq "0"}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Edit/Create{/tr}</a></span>
{/if}
</div>
{/if}

{* ----------------------------------- *}
{cycle name=content values="1,2,3" print=false advance=false}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<div class="tabrow">
<table cellpadding="0" cellspacing="0" border="0">
<tr><td class="middle" nowrap="nowrap" rowspan="2">
{if $feature_jscalendar eq 'y'}
<form action="tiki-calendar.php" method="post" name="f">
<span title="{tr}Date Selector{/tr}" id="datrigger" class="daterow" >{$focusdate|tiki_long_date}</span>
<span class="date">&lt;- {tr}click to navigate{/tr}</span>
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
<tr><td><a href="tiki-calendar.php?todate={$daybefore}" class="link" title="{$daybefore|tiki_long_date}">&laquo;&nbsp;{tr}day{/tr}</a></td><td style="text-align:center; padding-right:5px; padding-left: 5px;" class="middle" rowspan="3" nowrap="nowrap"><b>{tr}Focus:{/tr}<br />{$focusdate|tiki_long_date}</b></td><td><a href="tiki-calendar.php?todate={$dayafter}" class="link" title="{$dayafter|tiki_long_date}">{tr}day{/tr}&nbsp;&raquo;</a></td></tr>
<tr><td><a href="tiki-calendar.php?todate={$weekbefore}" class="link" title="{$weekbefore|tiki_long_date}">&laquo;&nbsp;{tr}week{/tr}</a></td><td><a href="tiki-calendar.php?todate={$weekafter}" class="link" title="{$weekafter|tiki_long_date}">{tr}week{/tr}&nbsp;&raquo;</a></td></tr>
<tr><td><a href="tiki-calendar.php?todate={$monthbefore}" class="link" title="{$monthbefore|tiki_long_date}">&laquo;&nbsp;{tr}month{/tr}</a></td><td><a href="tiki-calendar.php?todate={$monthafter}" class="link" title="{$monthafter|tiki_long_date}">{tr}month{/tr}&nbsp;&raquo;</a></td></tr>
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
<div><a href="tiki-calendar.php?todate={$nowUser}" class="linkmodule" title="{tr}change focus{/tr}"><b>{tr}Today{/tr}:<br /></b> {$now|tiki_short_datetime}</a></div>
</td>
<td style="text-align:right;" align="right" nowrap="nowrap">
<a href="tiki-calendar.php?viewmode=day{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'day'}on{else}off{/if}" title="{tr}day{/tr}"><img src="img/icons/cal_day.gif" width="30" height="24" border="0" alt="{tr}day{/tr}" align="top" /></a>
<a href="tiki-calendar.php?viewmode=week{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'week'}on{else}off{/if}" title="{tr}week{/tr}"><img src="img/icons/cal_week.gif" width="30" height="24" border="0" alt="{tr}week{/tr}" align="top" /></a>
<a href="tiki-calendar.php?viewmode=month{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'month'}on{else}off{/if}" title="{tr}month{/tr}"><img src="img/icons/cal_month.gif" width="30" height="24" border="0" alt="{tr}month{/tr}" align="top" /></a><br />
<a href="tiki-calendar.php?viewmode=quarter{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'quarter'}on{else}off{/if}" title="{tr}quater{/tr}"><img src="img/icons/cal_quarter.gif" width="30" height="24" border="0" alt="{tr}quarter{/tr}" align="top" /></a>
<a href="tiki-calendar.php?viewmode=semester{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'semester'}on{else}off{/if}" title="{tr}semester{/tr}"><img src="img/icons/cal_semester.gif" width="30" height="24" border="0" alt="{tr}semester{/tr}" align="top" /></a>
<a href="tiki-calendar.php?viewmode=year{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="viewmode{if $viewmode eq 'year'}on{else}off{/if}" title="{tr}year{/tr}"><img src="img/icons/cal_year.gif" width="30" height="24" border="0" alt="{tr}year{/tr}" align="top" /></a><br /></td></tr>
<tr><td style="text-align:center; padding-top:5px; padding-bottom: 5px">{if $viewlist eq 'list'}<a href="tiki-calendar.php?viewlist=table{if $viewmonth}&amp;mon={$viewmonth}&amp;day={$viewday}&amp;year={$viewyear}{/if}" class="linkbut" title="{tr}calendar view{/tr}">{tr}calendar view{/tr}</a>{else}<a href="tiki-calendar.php?viewlist=list" class="linkbut" title="{tr}list view{/tr}">{tr}list view{/tr}</a>{/if}
</td></tr>

<tr><td colspan="5" class="calnavigation">
{if $viewmode eq "day"}
{assign var="dBefore" value=$viewday-1}
{assign var="dAfter" value=$viewday+1}
<a href="tiki-calendar.php?mon={$viewmonth}&amp;day={$dBefore}&amp;year={$viewyear}" title="&laquo; {tr}day{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$viewmonth}&amp;day={$dAfter}&amp;year={$viewyear}" title="{tr}day{/tr} &raquo;">&raquo;</a>&nbsp;{$viewstart|user_date_format}
{/if}
{if $viewmode eq "week"}
{assign var="dBefore" value=$viewday-7}
{assign var="dAfter" value=$viewday+7}
<a href="tiki-calendar.php?mon={$viewmonth}&amp;day={$dBefore}&amp;year={$viewyear}" title="&laquo; {tr}week{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$viewmonth}&amp;day={$dAfter}&amp;year={$viewyear}" title="{tr}week{/tr} &raquo;">&raquo;</a>&nbsp;{$viewstart|user_date_format} - {$viewend|user_date_format}
{/if}
{if $viewmode eq "month"}
{assign var="mBefore" value=$viewmonth-1}
{assign var="mAfter" value=$viewmonth+1}
<a href="tiki-calendar.php?mon={$mBefore}&amp;day=1&amp;year={$viewyear}" title="&laquo; {tr}month{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$mAfter}&amp;day=1&amp;year={$viewyear}" title="{tr}month{/tr}&raquo;">&raquo;</a>&nbsp;{$daystart|user_date_format:"%B %Y"}
{/if}
{if $viewmode eq "quarter"}
{assign var="mBefore" value=$viewmonth-3}
{assign var="mAfter" value=$viewmonth+3}
<a href="tiki-calendar.php?mon={$mBefore}&amp;day=1&amp;year={$viewyear}" title="&laquo; {tr}quater{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$mAfter}&amp;day=1&amp;year={$viewyear}" title="{tr}quater{/tr} &raquo;">&raquo;</a>&nbsp;{$daystart|user_date_format:"%B %Y"} - {$dayend|user_date_format:"%B %Y"}
{/if}
{if $viewmode eq "semester"}
{assign var="mBefore" value=$viewmonth-6}
{assign var="mAfter" value=$viewmonth+7}
<a href="tiki-calendar.php?mon={$mBefore}&amp;day=1&amp;year={$viewyear}" title="&laquo; {tr}semester{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$mAfter}&amp;day=1&amp;year={$viewyear}" title="{tr}semester{/tr} &raquo;">&raquo;</a>&nbsp;{$daystart|user_date_format:"%B %Y"} - {$dayend|user_date_format:"%B %Y"}
{/if}
{if $viewmode eq "year"}
{assign var="yBefore" value=$viewyear-1}
{assign var="yAfter" value=$viewyear+1}
<a href="tiki-calendar.php?mon={$viewmonth}&amp;day=1&amp;year={$yBefore}" title="&laquo; {tr}year{/tr}">&laquo;</a>&nbsp;<a href="tiki-calendar.php?mon={$viewmonth}&amp;day=1&amp;year={$yAfter}" title="{tr}year{/tr} &raquo;">&raquo;</a>&nbsp;{$daystart|user_date_format:"%Y"}
{/if}
</td></tr>
</table>
</div>

{if $viewlist eq 'list'}
<br />
{if $displayedcals}
<table class="normal">
<tr><th class="heading" width="20%"><a class="tableheading" href="tiki-calendar.php?sort_mode={if $sort_mode eq 'start_desc'}start_asc{else}start_desc{/if}">{tr}Start{/tr}</a></th><th class="heading" width="20%"><a class="tableheading" href="tiki-calendar.php?sort_mode={if $sort_mode eq 'end_desc'}end_asc{else}end_desc{/if}">{tr}End{/tr}</a></th><th class="heading"><a class="tableheading" href="tiki-calendar.php?sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a><th class="heading">{tr}action{/tr}</th></tr>
{if $listevents|@count eq 0}<tr><td colspan="5">{tr}No records found{/tr}</td></tr>{/if}
{cycle values="odd,even" print=false}
{section name=w loop=$listevents}
<tr class="{cycle} ">
<td><a href="tiki-calendar.php?todate={$listevents[w].startUser}" title="{tr}change focus{/tr}">{$listevents[w].start|tiki_short_date}</a><br />{$listevents[w].start|tiki_short_time}</a></td>
<td>{if $listevents[w].start|tiki_short_date ne $listevents[w].end|tiki_short_date}<a href="tiki-calendar.php?todate={$listevents[w].endUser}" title="{tr}change focus{/tr}">{$listevents[w].end|tiki_short_date}</a>{/if}<br />{if $listevents[w].start ne $listevents[w].end}{$listevents[w].end|tiki_short_time}{/if}</td>
<td>
<a class="link" href="tiki-calendar.php?calitemId={$listevents[w].calitemId}&amp;editmode=add{if $feature_tabs ne 'y'}#add{/if}" title="{tr}edit{/tr}">{$listevents[w].name}</a><br />
<span style= "font-style:italic">{$listevents[w].parsedDescription}</span>
{if $listevents[w].web}
<br /><a href="{$listevents[w].web}" target="_other" class="calweb" title="{$listevents[w].web}"><img src="img/icons/external_link.gif" width="7" height="7" alt=">" /></a>
{/if}
</td>
<td>{if $listevents[w].modifiable eq "y"}<a class="link" href="tiki-calendar.php?calitemId={$listevents[w].calitemId}&amp;editmode=add{if $feature_tabs ne 'y'}#add{/if}" title="{tr}edit{/tr}"><img src="img/icons/edit.gif" border="0"  width="20" height="16" alt="{tr}edit{/tr}" /></a><a class="link" href="tiki-calendar.php?calitemId={$listevents[w].calitemId}&amp;delete=1" title="{tr}remove{/tr}"><img src="img/icons2/delete.gif" border="0" width="16" height="16" alt='{tr}remove{/tr}'>{/if}</td></tr>
{/section}
</td></tr>
</table>
{/if}

{if $displayedtikicals}
<table class="normal">
<tr><th class="heading" width="20%">{tr}Date{/tr}</th><th class="heading">{tr}Name{/tr}</th></tr>
{if $listtikievents|@count eq 0}<tr><td colspan="5">{tr}No records found{/tr}</td></tr>{/if}
{cycle values="odd,even" print=false}
{foreach from=$listtikievents item=tikievents key=start}
{section name=w loop=$tikievents}
<tr class="{cycle} ">
<td><a href="tiki-calendar.php?todate={$tikievents[w].start}">{$tikievents[w].start|tiki_short_date}</a><br />{$tikievents[w].start|tiki_short_time}</td>
<td>
<a href="{$tikievents[w].url}" title="{tr}details{/tr}">{$tikievents[w].name}</a><br /><span style= "font-style:italic">{$tikievents[w].description}</span>
</td></tr>
{/section}
{/foreach}
</td></tr>
</table>
{/if}

{elseif $viewmode eq 'day'}
<table cellpadding="0" cellspacing="0" border="0"  id="caltable">
<tr><td width="42" class="heading">{tr}Hours{/tr}</td><td class="heading">{tr}Events{/tr}</td></tr>
{cycle values="odd,even" print=false}
{foreach key=k item=h from=$hours}
<tr><td width="42" class="{cycle advance=false}">{$h}{tr}h{/tr}</td>
<td class="{cycle}">
{section name=hr loop=$hrows[$h]}
<div {if $hrows[$h][hr].calname ne ""}class="Cal{$hrows[$h][hr].type}"{/if} style="clear:both">
{$hours[$h]}:{$hrows[$h][hr].mins} : {if $hrows[$h][hr].calname eq ""}{$hrows[$h][hr].type} : {/if}
<a href="{$hrows[$h][hr].url}" class="linkmenu">{$hrows[$h][hr].name}</a>
{if $hrows[$h][hr].calname ne ""}{$hrows[$h][hr].parsedDescription}{else}{$hrows[$h][hr].description}{/if}{if ($calendar_view_tab eq "y" or $tiki_p_change_events eq "y") and $hrows[$h][hr].calname ne ""}<span  style="float:right;">{if $calendar_view_tab eq "y"}<a href="tiki-calendar.php?calitemId={$hrows[$h][hr].calitemId}&amp;editmode=details"{if $feature_tabs ne 'y'}#details{/if}" title="{tr}details{/tr}"><img src="img/icons/zoom.gif" border="0" width="16" height="16" alt="{tr}zoom{/tr}" /></a>&nbsp;{/if}{if $hrows[$h][hr].modifiable eq "y"}<a href="tiki-calendar.php?calitemId={$hrows[$h][hr].calitemId}&amp;editmode=1{if $feature_tabs ne 'y'}#add{/if}" title="{tr}edit{/tr}"><img src="img/icons/edit.gif" border="0"  width="20" height="16" alt="{tr}edit{/tr}" /></a><a href="tiki-calendar.php?calitemId={$hrows[$h][hr].calitemId}&amp;delete=1"  title="{tr}remove{/tr}" /><img src="img/icons2/delete.gif" border="0" width="16" height="16" alt="{tr}remove{/tr}" /></a>{/if}</span>{/if}
</div>
{/section}
</td></tr>
{/foreach}
</table>
{else}
<table cellpadding="0" cellspacing="0" border="0"  id="caltable">
<tr><td width="2%">&nbsp;</td>
{section name=dn loop=$daysnames}
<td class="heading"  width="14%">{$daysnames[dn]}</td>
{/section}
</tr>
{cycle values="odd,even" print=false}
{section name=w loop=$cell}
<tr><td width="2%" class="heading">{$weeks[w]}</td>
{section name=d loop=$weekdays}
{if $cell[w][d].focus}
{cycle values="odd,even" print=false advance=false}
{else}
{cycle values="odddark" print=false advance=false}
{/if}
<td class="{cycle}" width="14%">
<div align="center" class="calfocus{if $cell[w][d].day eq $focuscell}on{/if}">
<span style="float:left">
<a href="tiki-calendar.php?todate={$cell[w][d].day}" title="{tr}change focus{/tr}">{$cell[w][d].day|date_format:$short_format_day}</a> {* day is unix timestamp *}
</span>
<span style="float:right;margin-right:3px;padding-right:4px;">
{if $tiki_p_add_events eq 'y' and count($listcals) > 0}
<a href="tiki-calendar.php?todate={$cell[w][d].day}&amp;editmode=add{if $feature_tabs ne 'y'}#add{/if}" title="{tr}add item{/tr}">{tr}+{/tr}</a>
{/if}
</span>
.<br />
</div>
<div class="calcontent">
{section name=items loop=$cell[w][d].items}
{assign var=over value=$cell[w][d].items[items].over}
<div class="Cal{$cell[w][d].items[items].type} calId{$cell[w][d].items[items].calendarId}" {if $cell[w][d].items[items].calitemId eq $calitemId and $calitemId|string_format:"%d" ne 0}style="padding:5px;border:1px solid black;"{/if}>
<span class="calprio{$cell[w][d].items[items].prio}" style="padding-left:3px;padding-right:3px;"><a {if $calendar_view_tab eq "y" || $cell[w][d].items[items].modifiable eq "y" || $cell[w][d].items[items].calname eq ""}href="{$cell[w][d].items[items].url}{if $feature_tabs ne 'y'}#add{/if}"{/if} {if $calendar_sticky_popup eq "y" and $cell[w][d].items[items].calitemId}{popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}{else}{popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}{/if}
class="linkmenu">{$cell[w][d].items[items].name|truncate:$trunc:".."|default:"..."}</a></span>
{if $cell[w][d].items[items].web}
<a href="{$cell[w][d].items[items].web}" target="_other" class="calweb" title="{$cell[w][d].items[items].web}"><img src="img/icons/external_link.gif" width="7" height="7" alt=">" border="0"/></a>
{/if}
{if $cell[w][d].items[items].nl}
<a href="tiki-newsletters.php?nlId={$cell[w][d].items[items].nl}&info=1" class="calweb" title="Subscribe"><img src="img/icons/external_link.gif" width="7" height="7" alt=">" border="0"/></a>
{/if}
<br />
</div>
{/section}
</div>
</td>
{/section}
</tr>
{/section}
</table>
{/if}
</div>

{* ----------------------------------- *}
<a name="filter" />
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<form class="box" method="get" action="tiki-calendar.php" name="f">
<table border="0" >
<tr>
<td>
<input type="submit" name="refresh" value="{tr}Refresh{/tr}"/><br />
</td>

{* si au moins qqle chose *}
<td>
<div class="caltitle">{tr}Group Calendars{/tr}</div>
<div class="caltoggle"><input name="calswitch" id="calswitch" type="checkbox" onchange="switchCheckboxes(this.form,'calIds[]',this.checked);"/> <label for="calswitch">{tr}check / uncheck all{/tr}</label></div>
{foreach item=k from=$listcals}
<div class="calcheckbox"><input type="checkbox" name="calIds[]" value="{$k|escape}" id="groupcal_{$k}" {if $thiscal.$k}checked="checked"{/if} />
<label for="groupcal_{$k}" class="calId{$k}">{$infocals.$k.name} (id #{$k})</label>
</div>
{/foreach}
</td>

<td>
{if $tiki_p_view_tiki_calendar eq "y"}
<div class="caltitle">{tr}Tools Calendars{/tr}</div>
<div class="caltoggle"><input name="tikiswitch" id="tikiswitch" type="checkbox" onclick="switchCheckboxes(this.form,'tikicals[]',this.checked);" /> <label for="tikiswitch">{tr}check / uncheck all{/tr}</label></div>
{foreach from=$tikiItems key=ki item=vi}
{if $vi.feature eq 'y' and $vi.right eq 'y'}
<div class="calcheckbox"><input type="checkbox" name="tikicals[]" value="{$ki|escape}" id="tikical_{$ki}" {if $tikical.$ki}checked="checked"{/if} />
<label for="tikical_{$ki}" class="Cal{$ki}"> = {$vi.label}</label></div>
{/if}
{/foreach}
{/if}
</td>
</tr></table>
</form>
</div>
{* ----------------------------------- *}

<a name="add" id="add"/>
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
{if $modifTab or $modifTab eq "0"}

{* ................................................................................... *}
{if (($calitemId > 0 and $modifiable eq 'y') or ($tiki_p_add_events eq 'y' and $calitemId == 0)) && $editmode ne "details"}

{* .............................................. *}{if $calitemId}
<h2>{tr}Edit Calendar Item{/tr}</h2>
<div><b>{$calname}<br />{$name|default:"new event"}</b> (id #{$calitemId})</div>
<div class="mininotes">{tr}Created{/tr}: {$created|tiki_long_date} {$created|tiki_short_time} </div>
<div class="mininotes">{tr}Modified{/tr}: {$lastModif|tiki_long_date} {$lastModif|tiki_short_time} </div>
<div class="mininotes">{tr}by{/tr}: {$lastUser} </div>
{* ................................................ *}{else}
<h2>{tr}New Calendar Item{/tr}</h2>
<div><b>{$calname}</b> </div>
{* .................................... *}{/if}
{if $preview}
<h3>{tr}Preview{/tr}</h3>
<div class="wikitext">{$parsedDescription}</div>
{/if}
<form enctype="multipart/form-data" method="post" action="tiki-calendar.php" id="editcalitem" name="formAddItem" style="display:block;">
<input type="hidden" name="editmode" value="in">
{if $tiki_p_change_events and $calitemId}
<input type="hidden" name="calitemId" value="{$calitemId}">
<input type="hidden" name="created" value="{$created}">
<input type="hidden" name="lastModif" value="{$lastModif}">
<input type="hidden" name="lastUser" value="{$lastUser}">
<input type="hidden" name="calname" value="{$calname}">
{/if}

<table class="normal">
<tr><td class="formcolor">{tr}Calendar{/tr}</td><td class="formcolor">
<select name="calendarId" onchange="formAddItem.submit();">
{foreach item=lc from=$listcals}
{if ($infocals.$lc.tiki_p_add_events eq "y" or $infocals.$lc.tiki_change_events eq "y")}
<option value="{$lc|escape}" {if $defaultAddCal eq $lc}selected="selected"{/if}>{$infocals.$lc.name}</option>
{/if}
{/foreach}
</select>
</td></tr>

{if $customcategories eq 'y'}
<tr><td class="formcolor">{tr}Category{/tr}</td><td class="formcolor">
<select name="categoryId">
{section name=t loop=$listcat}
{if $listcat[t]}
<option value="{$listcat[t].categoryId|escape}" {if $categoryId eq $listcat[t].categoryId}selected="selected"{/if}>{$listcat[t].name}</option>
{/if}
{/section}
</select>
{tr}or create a new category{/tr} 
<input type="text" name="newcat" value="">
</td></tr>
{/if}

{if $customlocations eq 'y'}
<tr><td class="formcolor">{tr}Location{/tr}</td><td class="formcolor">
<select name="locationId">
{section name=l loop=$listloc}
{if $listloc[l]}
<option value="{$listloc[l].locationId|escape}" {if $locationId eq $listloc[l].locationId}selected="selected"{/if}>{$listloc[l].name}</option>
{/if}
{/section}
</select>
{tr}or create a new location{/tr} 
<input type="text" name="newloc" value="">
</td></tr>
{/if}

{if $customparticipants eq 'y'}
<tr><td class="formcolor">{tr}Organized by{/tr}</td><td class="formcolor">
<input type="text" name="organizers" value="{$organizers|escape}" id="organizers">
{tr}comma separated usernames{/tr}
</td></tr>

<tr><td class="formcolor">{tr}Participants{/tr}</td><td class="formcolor">
<input type="text" name="participants" value="{$participants|escape}" id="participants">
{tr}comma separated username:role{/tr} 
{tr}with roles{/tr} {tr}Chair{/tr}:0, {tr}Required{/tr}:1, {tr}Optional{/tr}:2, {tr}None{/tr}:3
</td></tr>
{/if}

<tr><td  class="formcolor">{tr}Start{/tr}</td><td class="formcolor">
{if $feature_jscalendar eq 'y'}
<input type="hidden" name="start_date_input" value="{$start}" id="start_date_input" />
<a href="#"><span id="start_date_display" class="daterow">{$start|date_format:$daformat}</span></a>
<script type="text/javascript">
{literal}Calendar.setup( { {/literal}
date        : "{$start|date_format:"%B %e, %Y %H:%M"}",      // initial date
inputField  : "start_date_input",      // ID of the input field
ifFormat    : "%s",    // the date format
displayArea : "start_date_display",       // ID of the span where the date is to be shown
daFormat    : "{$daformat}",  // format of the displayed date
showsTime   : true,
singleClick : true,
align       : "bR",
firstDay : {$firstDayofWeek},
timeFormat : {$timeFormat12_24}
{literal} } );{/literal}
</script>
{else}
{if $start_freeform_error eq 'y'}<span class="attention">{tr}Syntax error{/tr}</span><br />{/if}
<input type="text" name="start_freeform" value="{$start_freeform}">
<a {popup text="{tr}Format: mm/dd/yyyy hh:mm<br />...{/tr} {tr}See strtotime php function{/tr}"}><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}'></a>
{tr}or{/tr}
{html_select_date time=$start prefix="start_" end_year="+4" field_order=DMY}
{html_select_time minute_interval=5 time=$start prefix="starth_" display_seconds=false use_24_hours=true}
{/if}
</td></tr>

<tr><td class="formcolor">{tr}End{/tr}</td><td class="formcolor">{if $end_error eq 'y'}<span class="attention">{tr}Error{/tr}</span><br />{/if}
<input type="radio" name="endChoice" value="date" checked="checked" />
{if $feature_jscalendar eq 'y'}
<input type="hidden" name="end_date_input" value="{$end}" id="end_date_input" />
<a href="#"><span id="end_date_display" class="daterow">{$end|date_format:$daformat}</span></a>
<script type="text/javascript">
{literal}Calendar.setup( { {/literal}
date        : "{$end|date_format:"%B %e, %Y %H:%M"}",      // initial date
inputField  : "end_date_input",      // ID of the input field
ifFormat    : "%s",    // the date format
displayArea : "end_date_display",       // ID of the span where the date is to be shown
daFormat    : "{$daformat}",  // format of the displayed date
showsTime   : true,
singleClick : true,align       : "bR",
firstDay : {$firstDayofWeek},
timeFormat : {$timeFormat12_24}
{literal} } );{/literal}
</script>
{else}
{if $end_freeform_error eq 'y'}<span class="attention">{tr}Syntax error{/tr}</span><br />{/if}
<input type="text" name="end_freeform" value="{$end_freeform}">
<a {popup text="{tr}Format: mm/dd/yyy hh:mm<br />...{/tr} {tr}See strtotime php function{/tr}"}><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}'></a>
 {tr}or{/tr}
{html_select_date time=$end prefix="end_" end_year="+4" field_order=DMY}
{html_select_time minute_interval=5 time=$end prefix="endh_" display_seconds=false use_24_hours=true}
{/if}
</td></tr>
<tr><td class="formcolor">{tr}Duration{/tr}</td><td class="formcolor"><input type="radio" name="endChoice" value="duration" />
<input type="text" size="3" name="duration_hours" value="{$duration_hours}" />{if $duration_hours > 1}{tr}hours{/tr}{else}{tr}hour{/tr}{/if}&nbsp;
<select name="duration_minutes">{html_options options=$mrows selected=$duration_minutes}</select>{if $duration_minutes > 1}{tr}minutes{/tr}{else}{tr}minute{/tr}{/if}
</td>
</tr>

<tr><td class="formcolor">{tr}Title{/tr}</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" />
</td></tr>
<tr><td  class="formcolor">{tr}Description{/tr}<br /><br />{include file="textareasize.tpl" area_name='description' formId='editcalitem'}<br /><br />
{include file=tiki-edit_help_tool.tpl area_name="description"}</td><td class="formcolor">
<textarea class="wikiedit" name="description" rows="{$rows}" cols="{$cols}" id="description" wrap="virtual">{$description|escape}</textarea>
</td></tr>

<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}preview{/tr}" /></td></tr>

<tr><td class="formcolor">{tr}URL{/tr}</td><td class="formcolor"><input type="text" name="url" value="{$url|escape}" />
</td></tr>

{if $custompriorities eq 'y'}
<tr><td class="formcolor">{tr}Priority{/tr}</td><td class="formcolor">
<select name="priority">
<option value="1" {if $priority eq 1}selected="selected"{/if} class="calprio1">1</option>
<option value="2" {if $priority eq 2}selected="selected"{/if} class="calprio2">2</option>
<option value="3" {if $priority eq 3}selected="selected"{/if} class="calprio3">3</option>
<option value="4" {if $priority eq 4}selected="selected"{/if} class="calprio4">4</option>
<option value="5" {if $priority eq 5}selected="selected"{/if} class="calprio5">5</option>
<option value="6" {if $priority eq 6}selected="selected"{/if} class="calprio6">6</option>
<option value="7" {if $priority eq 7}selected="selected"{/if} class="calprio7">7</option>
<option value="8" {if $priority eq 8}selected="selected"{/if} class="calprio8">8</option>
<option value="9" {if $priority eq 9}selected="selected"{/if} class="calprio9">9</option>
</select>
</td></tr>
{/if}

<tr><td class="formcolor">{tr}Status{/tr}</td><td class="formcolor">
<select name="status">
<option value="0" {if $status eq '0'}selected="selected"{/if}>0:{tr}Tentative{/tr}</option>
<option value="1" {if $status eq '1'}selected="selected"{/if}>1:{tr}Confirmed{/tr}</option>
<option value="2" {if $status eq '2'}selected="selected"{/if}>2:{tr}Cancelled{/tr}</option>
</select>
</td></tr>

{if $customlanguages eq 'y'}
<tr><td class="formcolor">{tr}Language{/tr}</td><td class="formcolor">
<select name="lang">
{section name=ix loop=$languages}
<option value="{$languages[ix].value|escape}"
  {if $lang eq $languages[ix].value}selected="selected"{/if}>
  {$languages[ix].name}
</option>
{/section}
</select>
</td></tr>
{/if}

{if $customsubscription eq 'y'}
<tr><td class="formcolor">{tr}Subscription List{/tr}</td><td class="formcolor">
<select name="nlId">
{section name=l loop=$subscrips}
{if $subscrips[l]}
<option value="{$subscrips[l].nlId|escape}" {if $nlId eq $subscrips[l].nlId}selected="selected"{/if}>{$subscrips[l].name}</option>
{/if}
{/section}
<option value=0>{tr}None{/tr}</option>
</select>
</td></tr>
{/if}

<tr><td class="formcolor">&nbsp;</td><td class="formcolor">
<span  style="float:right;"><a href="tiki-calendar.php?calitemId={$calitemId}&amp;delete=1"  title="{tr}remove{/tr}" /><img src="img/icons2/delete.gif" border="0" width="16" height="16" alt="{tr}remove{/tr}" /></a></span>
<input type="submit" name="save" value="{tr}save{/tr}" />
{if $calitemId and $tiki_p_change_events}
<input type="submit" name="copy" value="{tr}duplicate{/tr}" />
{/if}
{tr}save_to{/tr}
<select name="calendarId2">
{foreach item=lc from=$listcals}
{if ($infocals.$lc.tiki_p_add_events eq "y" or $infocals.$lc.tiki_change_events eq "y")}
<option value="{$lc|escape}" {if $defaultAddCal eq $lc}selected="selected"{/if}>{$infocals.$lc.name}</option>
{/if}
{/foreach}
</select>

</td></tr>
</table>
</form>
{/if}{/if} {* modifTab *}
{if $calitemId}
<a name="details" />
<h2>{$name}</h2>
{if $editmode eq "details" and $tiki_p_change_events eq 'y'}<div style="text-align:right"><a href="tiki-calendar.php?calitemId={$calitemId}&amp;editmode=add{if $feature_tabs ne 'y'}#add{/if}" title="{tr}edit{/tr}"><img src="img/icons/edit.gif" border="0"  width="20" height="16" alt="{tr}edit{/tr}" /></a><a href="tiki-calendar.php?calitemId={$calitemId}&amp;delete=1" title="{tr}remove{/tr}"><img src="img/icons2/delete.gif" border="0" width="16" height="16" alt="{tr}remove{/tr}" /></a></div>{/if}
{cycle values="odd, even" print=false}
<table class="normal">
<tr><td class="{cycle advance=false}">{tr}Name{/tr}</td><td class="{cycle}">{$name}</td></tr>
<tr><td class="{cycle advance=false}">{tr}Calendar{/tr}</td><td class="{cycle}">{$calname}</td></tr>
<tr><td class="{cycle advance=false}">{tr}Start{/tr}</td><td class="{cycle}">{$start|tiki_long_date}<br />{$start|tiki_short_time}</td></tr>
<tr><td class="{cycle advance=false}">{tr}End{/tr}</td><td class="{cycle}">{$end|tiki_long_date}<br />{$end|tiki_short_time}</td></tr>
<tr><td class="{cycle advance=false}">{tr}Duration{/tr}</td><td class="{cycle}">{if $duration_hours > 1}{$duration_hours} {tr}hours{/tr}{elseif $duration_hours eq 1}1 {tr}hour{/tr}{/if}{if $duration_minutes > 0} - {$duration_minutes} {if $duration_minutes > 1}{tr}minutes{/tr}{else}{tr}minute{/tr}{/if}{/if}</td></tr>
<tr><td class="{cycle advance=false}">{tr}Description{/tr}</td><td class="{cycle}">{$parsedDescription}</td></tr>

<tr><td class="{cycle advance=false}">{tr}Created{/tr}</td><td class="{cycle}">{$created|tiki_short_datetime}</td></tr>
<tr><td class="{cycle advance=false}">{tr}Modified{/tr}</td><td class="{cycle}">{$lastModif|tiki_short_datetime}</td></tr>
<tr><td class="{cycle advance=false}">{tr}by{/tr}</td><td class="{cycle}">{$lastUser}</td></tr>
{if $customcategories eq 'y'}<tr><td class="{cycle advance=false}">{tr}Category{/tr}</td><td class="{cycle}">{$listcat[$categoryId].name}</td></tr>{/if}
{if $customlocations eq 'y'}<tr><td class="{cycle advance=false}">{tr}Location{/tr}</td><td class="{cycle}">{$listloc[$locationId].name}</td></tr>{/if}
{if $customparticipants eq 'y'}<tr><td class="{cycle advance=false}">{tr}Organized by{/tr}</td><td class="{cycle}">{$organizers|escape}</td></tr><tr><td class="{cycle advance=false}">{tr}Participants{/tr}</td><td class="{cycle}">{$participants|escape}</td></tr>{/if}
<tr><td class="{cycle advance=false}">{tr}URL{/tr}</td><td class="{cycle}">{if $url}<a href="{$url}">{$url}</a>{else}&nbsp;{/if}</td></tr>
{if $custompriorities eq 'y'}<tr><td class="{cycle advance=false}">{tr}Priority{/tr}</td><td class="{cycle}"><span class="calprio{$priority}">{$priority}</span></td></tr>{/if}
<tr><td class="{cycle advance=false}">{tr}Status{/tr}</td><td class="{cycle}"><span class="Cal{$status}">{if $status eq '0'}{tr}Tentative{/tr}{elseif $status eq '1'}{tr}Confirmed{/tr}{else}{tr}Cancelled{/tr}{/if}</span></td></tr>
{if $customlanguages eq 'y'}<tr><td class="{cycle advance=false}">{tr}Language{/tr}</td><td class="{cycle}">{$lang}</td></tr>{/if}

{if $customsubscription eq 'y'}<tr><td class="{cycle advance=false}">{tr}Subscription List{/tr}</td><td class="{cycle}">
{foreach item=k from=$subscrips}{if $k.nlId eq $nlId}{$k.name}{/if}{/foreach}
<td></tr>{/if}

</table>
{/if} {*calitemId *}
{* ....................................................... 
<h2>{tr}Add Calendar Item{/tr}</h2>

<ul>
{foreach name=licals item=k from=$listcals}
{if $infocals.$k.tiki_p_add_events eq 'y'}
<li>{tr}in{/tr} <a href="tiki-calendar.php?calendarId={$k}&amp;todate={$focusdate}&amp;editmode=add{if $feature_tabs ne 'y'}#add{/if}" class="link">{$infocals.$k.name}</a></li>
{/if}
{/foreach}
</ul>
 ....................................................... *}
</div>
