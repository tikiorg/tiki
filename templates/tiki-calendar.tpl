{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-calendar.tpl,v 1.27 2003-12-01 14:45:47 mose Exp $ *}

{popup_init src="lib/overlib.js"}

<a class="pagetitle" href="tiki-calendar.php?view={$view}">{tr}Calendar{/tr}</a>
{if $tiki_p_admin_calendar eq 'y' or $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-admin_calendars.php" class="linkbut">{tr}admin{/tr}</a></span>
{/if}
<br />

{* ----------------------------------- *}

<div id="tab" style="display:{if $smarty.cookies.tab eq 'c' or $show_navtab}none{else}block{/if};">
<a href="javascript:show('tabcal',1);hide('tabnav',1);hide('tab',1);" class="caltab">{tr}Calendars Panel{/tr}</a>
<a href="javascript:hide('tabcal',1);show('tabnav',1);hide('tab',1);" class="caltab">{tr}Events Panel{/tr}</a>
</div>

{* ----------------------------------- *}

<div id="tabcal" style="display:{if $smarty.cookies.tabcal eq 'o' and !$show_navtab}block{else}none{/if};">
<div>
<a href="javascript:show('tabcal',1);hide('tabnav',1);hide('tab',1);" class="caltabon">{tr}Calendars Panel{/tr}</a>
<a href="javascript:hide('tabcal',1);show('tabnav',1);hide('tab',1);" class="caltab">{tr}Events Panel{/tr}</a>
<a href="javascript:hide('tabcal',1);hide('tabnav',1);show('tab',1);" class="caltab">{tr}Hide Panels{/tr}</a>
</div>

<form class="box" method="get" action="tiki-calendar.php" name="f">
<div class="tabcal">
<table border="0" >
<tr>
<td>
<input type="submit" name="refresh" value="{tr}Refresh{/tr}"/><br />
</td>
<td>
<div class="caltitle">{tr}Group Calendars{/tr}</div>
<div class="caltoggle"
onclick="document.getElementById('calswitch').click();document.getElementById('calswitch').checked=!document.getElementById('calswitch').checked;document.getElementById('calswitch').click();"
><input name="calswitch" id="calswitch" type="checkbox" onclick="switchCheckboxes(this.form.name,'calIds[]','calswitch');"/> {tr}check / uncheck all{/tr}</div>
{foreach item=k from=$listcals}
<div class="calcheckbox"
onclick="document.getElementById('groupcal_{$k}').checked=!document.getElementById('groupcal_{$k}').checked;"
onmouseout="this.style.textDecoration='none';" 
onmouseover="this.style.textDecoration='underline';"
><input type="checkbox" name="calIds[]" value="{$k|escape}" id="groupcal_{$k}" {if $thiscal.$k}checked="checked"{/if}
onclick="this.checked=!this.checked;"/>
{$infocals.$k.name} (id #{$k})
</div>
{/foreach}
</td>
<td>
<div class="caltitle">{tr}Tools Calendars{/tr}</div>
<div class="caltoggle"
onclick="document.getElementById('tikiswitch').click();document.getElementById('tikiswitch').checked=!document.getElementById('tikiswitch').checked;document.getElementById('tikiswitch').click();"
><input name="tikiswitch" id="tikiswitch" type="checkbox" onclick="switchCheckboxes(this.form.name,'tikicals[]','tikiswitch');this.checked=!this.checked;"> {tr}check / uncheck all{/tr}</a></div>
{foreach from=$tikiItems key=ki item=vi}
{if $vi.feature eq 'y' and $vi.right eq 'y'}
<div class="calcheckbox"
onclick="document.getElementById('tikical_{$ki}').checked=!document.getElementById('tikical_{$ki}').checked;"
onmouseout="this.style.textDecoration='none';"  
onmouseover="this.style.textDecoration='underline';" 
><input type="checkbox" name="tikicals[]" value="{$ki|escape}" id="tikical_{$ki}" {if $tikical.$ki}checked="checked"{/if} onclick="this.checked=!this.checked;"/>
<span class="Cal{$ki}"> = {$vi.label}</span></div>
{/if}
{/foreach}
</td>
</tr></table>
</div></div>
</form>

{* ----------------------------------- *}

<div id="tabnav" style="display:{if $smarty.cookies.tabnav eq 'o' or $show_navtab}block{else}none{/if};">
<div>
<a href="javascript:show('tabcal',1);hide('tabnav',1);hide('tab',1);" class="caltab">{tr}Calendars Panel{/tr}</a>
<a href="javascript:hide('tabcal',1);show('tabnav',1);hide('tab',1);" class="caltabon">{tr}Events Panel{/tr}</a>
<a href="javascript:hide('tabcal',1);hide('tabnav',1);show('tab',1);" class="caltab">{tr}Hide Panels{/tr}</a>
</div>

<div class="tabnav">
{* ······························· *}{if $calitemId > 0 or $calendarId > 0}

{* ················ *}{if $calitemId}
<div class="pagetitle">{tr}Edit Calendar Item{/tr} : </span>{$name|default:"new event"} {if $calitemId}(id #{$calitemId}){/if}</div>
<div class="mininotes">{tr}Created{/tr}: {$created|tiki_long_date} {$created|tiki_long_time} </div>
<div class="mininotes">{tr}Modified{/tr}: {$lastModif|tiki_long_date} {$lastModif|tiki_long_time} </div>
<div class="mininotes">{tr}by{/tr}: {$lastUser} </div>
{* ················ *}{/if}

<div>
<form enctype="multipart/form-data" method="post" action="tiki-calendar.php" id="editcalitem" name="f" style="display:block;">
<input type="hidden" name="editmode" value="1">
{if $tiki_p_change_events}
<input type="hidden" name="calitemId" value="{$calitemId|escape}">
{/if}
<table class="normal" style="width:100%;">
<tr><td class="formcolor">{tr}Calendar{/tr}</td><td class="formcolor">
<select name="calendarId">
{foreach item=lc from=$listcals}
<option value="{$lc|escape}" {if $calendarId eq $lc}selected="selected"{/if} onchange="document.forms[f].submit();">{$infocals.$lc.name}</option>
{/foreach}
</select>
<input type="submit" name="refr" value="{tr}refresh{/tr}" />
{if $calendarId}
<span class="mini">( {$calname} )</span>
{/if}
<br />
{tr}If you change the calendar selection, please refresh to get the appropriated list in Category, Location and people (if applicable to the calendar you choose).{/tr}<br />
</td></tr>

{if $customcategories eq 'y'}
<tr><td class="form">{tr}Category{/tr}</td><td class="form">
<select name="categoryId">
{section name=t loop=$listcat}
{if $listcat[t]}
<option value="{$listcat[t].categoryId|escape}" {if $categoryId eq $listcat[t].categoryId}selected="selected"{/if}>{$listcat[t].name}</option>
{/if}
{/section}
</select>
{tr}or create a new category{/tr} 
<input type="text" name="newcat" value="">
{if $categoryId}
<span class="mini">( {$categoryName} )</span>
{/if}
</td></tr>
{/if}

{if $customlocations eq 'y'}
<tr><td class="form">{tr}Location{/tr}</td><td class="form">
<select name="locationId">
{section name=l loop=$listloc}
{if $listloc[l]}
<option value="{$listloc[l].locationId|escape}" {if $locationId eq $listloc[l].locationId}selected="selected"{/if}>{$listloc[l].name}</option>
{/if}
{/section}
</select>
{tr}or create a new location{/tr} 
<input type="text" name="newloc" value="">
{if $locationId}
<span class="mini">( {$locationName} )</span>
{/if}
</td></tr>
{/if}

{if $customparticipants eq 'y'}
<tr><td class="form">{tr}Organized by{/tr}</td><td class="form">
<input type="text" name="organizers" value="{$organizers|escape}" id="organizers">
{tr}comma separated usernames{/tr}
</td></tr>

<tr><td class="form">{tr}Participants{/tr}</td><td class="form">
<input type="text" name="participants" value="{$participants|escape}" id="participants">
{tr}comma separated username:role{/tr} 
{tr}with roles{/tr} {tr}Chair{/tr}:0, {tr}Required{/tr}:1, {tr}Optional{/tr}:2, {tr}None{/tr}:3
</td></tr>
{/if}

<tr><td class="formcolor">{tr}Start{/tr}</td><td class="formcolor">
<input type="text" name="start_freeform" value=""> {tr}or{/tr}
{html_select_date time=$start prefix="start_" end_year="+4" field_order=DMY}
{html_select_time minute_interval=10 time=$start prefix="starth_" display_seconds=false use_24_hours=true}
</td></tr>

<tr><td class="formcolor">{tr}End{/tr}</td><td class="formcolor">
<input type="text" name="end_freeform" value=""> {tr}or{/tr}
{html_select_date time=$end prefix="end_" end_year="+4" field_order=DMY}
{html_select_time minute_interval=10 time=$end prefix="endh_" display_seconds=false use_24_hours=true}
</td></tr>

<tr><td class="formcolor">{tr}Name{/tr}</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" />
{if $name}<span class="mini">( {$name} )</span>{/if}
</td></tr>
<tr><td class="formcolor">{tr}Description{/tr}</td><td class="formcolor">
<textarea class="wikiedit" name="description" rows="8" cols="80" id="description" wrap="virtual">{$description|escape}</textarea>
{if $description}<div class="mini">( {$description} )</div>{/if}
</td></tr>

<tr><td class="formcolor">{tr}Url{/tr}</td><td class="formcolor"><input type="text" name="url" value="{$url|escape}" />
{if $url}<span class="mini">( <a href="{$url}">{$url}</a> )</span>{/if}
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
{if $priority}<span class="mini">( <span class="calprio{$priority}">{$priority}</span> )</span>{/if}
</td></tr>
{/if}

<tr><td class="formcolor">{tr}Status{/tr}</td><td class="formcolor">
<select name="status">
<option value="0" {if $status eq 0}selected="selected"{/if}><span class=""></span>0:{tr}Tentative{/tr}</option>
<option value="1" {if $status eq 1}selected="selected"{/if}>1:{tr}Confirmed{/tr}</option>
<option value="2" {if $status eq 2}selected="selected"{/if}>2:{tr}Cancelled{/tr}</option>
</select>
{if $calitemId}<span class="Cal{$status}"><span class="mini">( {$status} )</span></span>{/if}
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
{if $lang}<span class="mini">( {$lang} )</span>{/if}
</td></tr>
{/if}

<tr><td class="formcolor"></td><td class="formcolor">
{if $calitemId and $tiki_p_change_events}
<input type="submit" name="copy" value="{tr}duplicate{/tr}" />
{/if}
<input type="submit" name="save" value="{tr}save{/tr}" />
<a href="tiki-calendar.php?calitemId={$calitemId}&amp;delete=1" class="link" style="margin-left:42px;"/>{tr}delete{/tr}</a>
</td></tr>
</table>
</form>
</div>
{* ······························· *}{else}
<h2>{tr}Add Calendar Item{/tr}</h2>

{* ················ *}{if count($listcals) gt 0}
{tr}Choose a calendar where to put events{/tr} : 
<ul>
{foreach item=k from=$listcals}
<li><a href="tiki-calendar.php?todate={$focusdate}&amp;calendarId={$k}&amp;editmode=add" class="link">{$infocals.$k.name}</a></li>
{/foreach}
</ul>
{* ················ *}{else}
<h1>{tr}You should first ask that a calendar is created, so you can create events attached to it.{/tr}</h1>
{* ················ *}{/if}

{* ······························· *}{/if}
</div>
</div>

{* ----------------------------------- *}

<div class="tabrow">
<table cellpadding="0" cellspacing="0" border="0">
<tr><td class="middle" nowrap="nowrap">
{if $feature_jscalendar eq 'y'}
<form action="#" method="get">
<input type="text" id="todate" name="todate" value="" />
<button type="reset" id="trigger">...</button>
</form>
<script type="text/javascript">
{literal}Calendar.setup( { {/literal}
inputField  : "todate",      // ID of the input field
ifFormat    : "%d-%m-%Y",    // the date format
button      : "trigger"    // ID of the button
{literal} } );{/literal}
</script>
{else}
<div class="daterow">
<a href="tiki-calendar.php?todate={$monthbefore}" class="link" title="{$monthbefore|tiki_long_date}">{tr}-1m{/tr}</a>
<a href="tiki-calendar.php?todate={$weekbefore}" class="link" title="{$weekbefore|tiki_long_date}">{tr}-7d{/tr}</a>
<a href="tiki-calendar.php?todate={$daybefore}" class="link" title="{$daybefore|tiki_long_date}">{tr}-1d{/tr}</a> 
<b>{$focusdate|tiki_short_date}</b>
<a href="tiki-calendar.php?todate={$dayafter}" class="link" title="{$dayafter|tiki_long_date}">{tr}+1d{/tr}</a>
<a href="tiki-calendar.php?todate={$weekafter}" class="link" title="{$weekafter|tiki_long_date}">{tr}+7d{/tr}</a>
<a href="tiki-calendar.php?todate={$monthafter}" class="link" title="{$monthafter|tiki_long_date}">{tr}+1m{/tr}</a>
</div>
{/if}
</td>
<td align="center" width="100%" class="middle">
<div><b><a href="tiki-calendar.php?todate={$now}" class="linkmodule" title="{$now|tiki_long_date}">{tr}today{/tr} {$now|tiki_long_date}</a></b></div>
</td>
<td align="right" class="middle" nowrap="nowrap" width="90">
<a href="tiki-calendar.php?viewmode=day" class="viewmode{if $viewmode eq 'day'}on{else}off{/if}"><img src="img/icons/cal_day.gif" width="30" height="24" border="0" alt="{tr}day{/tr}" align="top" /></a><a 
href="tiki-calendar.php?viewmode=week" class="viewmode{if $viewmode eq 'week'}on{else}off{/if}"><img src="img/icons/cal_week.gif" width="30" height="24" border="0" alt="{tr}week{/tr}" align="top" /></a><a 
href="tiki-calendar.php?viewmode=month" class="viewmode{if $viewmode eq 'month'}on{else}off{/if}"><img src="img/icons/cal_month.gif" width="30" height="24" border="0" alt="{tr}month{/tr}" align="top" /></a>
</td></tr></table>
</div>

<table cellpadding="0" cellspacing="0" border="0"  id="caltable">
{if $viewmode eq 'day'}
<tr><td></td>
<td> nums</td><td>rows</td>
</tr>
{else}
<tr><td width="2%">&nbsp;</td>
{section name=dn loop=$daysnames}
<td class="heading"  width="14%">{$daysnames[dn]}</td>
{/section}
</tr>
{cycle values="odd,even" print=false}
{section name=w loop=$cell}
<tr><td width="2%" class="heading">{$weeks[w]}</td>
{section name=d loop=$weekdays}
{if $cell[w][d].day|date_format:"%m" eq $focusmonth}
{cycle values="odd,even" print=false advance=false}
{else}
{cycle values="odddark" print=false advance=false}
{/if}
<td class="{cycle}" width="14%">
<div align="center" class="calfocus{if $cell[w][d].day eq $focusdate}on{/if}">
<span style="float:left;">
<a href="tiki-calendar.php?todate={$cell[w][d].day}">{$cell[w][d].day|date_format:"%d/%m"}</a>
</span>
<span style="float:right;margin-right:3px;padding-right:4px;">
{if $tiki_p_add_events eq 'y' and count($listcals) > 0}
<a href="tiki-calendar.php?todate={$cell[w][d].day}&amp;editmode=add">{tr}+{/tr}</a>
{/if}
</span>
.<br />
</div>
{section name=items loop=$cell[w][d].items}
{assign var=over value=$cell[w][d].items[items].over}
<div class="Cal{$cell[w][d].items[items].type}" id="cal{$cell[w][d].items[items].type}" {if $cell[w][d].items[items].calitemId eq $calitemId and $calitemId|string_format:"%d" ne 0}style="padding:5px;border:1px solid black;"{/if}>
<span class="calprio{$cell[w][d].items[items].prio}" style="padding-left:3px;padding-right:3px;"><a href="{$cell[w][d].items[items].url}" {popup fullhtml="1" text="$over"} 
class="linkmenu">{$cell[w][d].items[items].name|truncate:22:".."|default:"..."}</a></span>
{if $cell[w][d].items[items].web}
<a href="{$cell[w][d].items[items].web}" target="_other" class="calweb" title="{$cell[w][d].items[items].web}">w</a>
{/if}
<br />
</div>
{/section}
</td>
{/section}
</tr>
{/section}
{/if}
</table>


