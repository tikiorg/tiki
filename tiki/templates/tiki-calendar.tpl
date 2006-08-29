{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-calendar.tpl,v 1.64 2006-08-29 20:19:13 sylvieg Exp $ *}
{popup_init src="lib/overlib.js"}

<h1><a class="pagetitle" href="tiki-calendar.php">{tr}Calendar{/tr}</a></h1>
{if $tiki_p_admin_calendar eq 'y' or $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-admin_calendars.php" class="linkbut">{tr}admin{/tr}</a></span>
{/if}
{if $feature_tabs ne 'y'}
<span class="button2"><a href="#filter" class="linkbut">{tr}filter{/tr}</a></span>
{if $modifTab}
<span class="button2"><a href="tiki-calendar.php?editmode=add&amp;calId={$calitemId}"class="linkbut">{tr}add item{/tr}</a></span>
{/if}
{/if}
<br /><br />

{if $feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3" print=false advance=false reset=true}
<div id="page-bar">
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Calendar{/tr}</a></span>
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Filter{/tr}</a></span>
{if $modifTab}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Edit/Create{/tr}</a></span>
{/if}
</div>
{/if}

{* ----------------------------------- *}
{cycle name=content values="1,2,3" print=false advance=false reset=true}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
{include file="tiki-show_calendar.tpl"}
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
<a class="link" href="tiki-calendar.php?calitemId={$listevents[w].calitemId}&amp;editmode=add#details" title="{tr}view{/tr}">{$listevents[w].name}</a><br />
<span style= "font-style:italic">{$listevents[w].parsedDescription}</span>
{if $listevents[w].web}
<br /><a href="{$listevents[w].web}" target="_other" class="calweb" title="{$listevents[w].web}"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;" /></a>
{/if}
</td>
<td>{if $listevents[w].modifiable eq "y"}<a class="link" href="tiki-calendar.php?calitemId={$listevents[w].calitemId}&amp;editmode=add{if $feature_tabs ne 'y'}#add{/if}" title="{tr}edit{/tr}"><img src="img/icons/edit.gif" border="0"  width="20" height="16" alt="{tr}edit{/tr}" /></a><a class="link" href="tiki-calendar.php?calitemId={$listevents[w].calitemId}&amp;delete=1" title="{tr}remove{/tr}"><img src="img/icons2/delete.gif" border="0" width="16" height="16" alt='{tr}remove{/tr}' />{/if}</td></tr>
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
{if $hrows[$h][hr].calname ne ""}{$hrows[$h][hr].parsedDescription}{else}{$hrows[$h][hr].description}{/if}{if ($calendar_view_tab eq "y" or $tiki_p_change_events eq "y") and $hrows[$h][hr].calname ne ""}<span  style="float:right;">{if $calendar_view_tab eq "y"}<a href="tiki-calendar.php?calitemId={$hrows[$h][hr].calitemId}&amp;editmode=details"{if $feature_tabs ne "y"}#details{/if} title="{tr}details{/tr}"><img src="img/icons/zoom.gif" border="0" width="16" height="16" alt="{tr}zoom{/tr}" /></a>&nbsp;{/if}{if $hrows[$h][hr].modifiable eq "y"}<a href="tiki-calendar.php?calitemId={$hrows[$h][hr].calitemId}&amp;editmode=1{if $feature_tabs ne 'y'}#add{/if}" title="{tr}edit{/tr}"><img src="img/icons/edit.gif" border="0"  width="20" height="16" alt="{tr}edit{/tr}" /></a><a href="tiki-calendar.php?calitemId={$hrows[$h][hr].calitemId}&amp;delete=1"  title="{tr}remove{/tr}"><img src="img/icons2/delete.gif" border="0" width="16" height="16" alt="{tr}remove{/tr}" /></a>{/if}</span>{/if}
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
<a href="{$cell[w][d].items[items].web}" target="_other" class="calweb" title="{$cell[w][d].items[items].web}"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;" border="0"/></a>
{/if}
{if $cell[w][d].items[items].nl}
<a href="tiki-newsletters.php?nlId={$cell[w][d].items[items].nl}&info=1" class="calweb" title="Subscribe"><img src="img/icons/external_link.gif" width="7" height="7" alt="&gt;" border="0"/></a>
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
<a name="filter" ></a>
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<form class="box" method="get" action="tiki-calendar.php" name="f">
<table border="0" >
<tr>
<td>
<input type="submit" name="refresh" value="{tr}Refresh{/tr}"/><br />
</td>

{* if at least something *}
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

<a name="add" id="add"></a>
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
{* ......................................................................... *}
{if $preview eq 'y' or $editmode eq 'add' or $feature_tabs eq 'y'}
{include file=tiki-calendar_add_event.tpl}
{/if}
{* ......................................................................... *}

{if $calitemId}
<a name="details" ></a>
<h2>{$name}</h2> {if $tiki_p_change_events eq 'y'} [<a href="tiki-calendar.php?calitemId={$calitemId}&amp;editmode=edit">edit</a>] {/if}
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
