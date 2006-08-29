{if $modifTab}

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

{* 
   ############################################################
              ----- FORM BEGIN
   ############################################################
*}

<form enctype="multipart/form-data" method="post" action="tiki-calendar.php" id="editcalitem" name="formAddItem" style="display:block;">
<input type="hidden" name="editmode" value="in" />
{if $tiki_p_change_events and $calitemId}
<input type="hidden" name="calitemId" value="{$calitemId}" />
<input type="hidden" name="created" value="{$created}" />
<input type="hidden" name="lastModif" value="{$lastModif}" />
<input type="hidden" name="lastUser" value="{$lastUser}" />
<input type="hidden" name="calname" value="{$calname}" />
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
<input type="text" name="newcat" value="" />
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
<input type="text" name="newloc" value="" />
</td></tr>
{/if}

{if $customparticipants eq 'y'}
<tr><td class="formcolor">{tr}Organized by{/tr}</td><td class="formcolor">
<input type="text" name="organizers" value="{$organizers|escape}" id="organizers" />
{tr}comma separated usernames{/tr}
</td></tr>

<tr><td class="formcolor">{tr}Participants{/tr}</td><td class="formcolor">
<input type="text" name="participants" value="{$participants|escape}" id="participants" />
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
singleClick : false,
align       : "bR",
firstDay : {$firstDayofWeek},
timeFormat : {$timeFormat12_24}
{literal} } );{/literal}
</script>
{else}
{if $feature_cal_manual_time eq 'y'}
{if $start_freeform_error eq 'y'}<span class="attention">{tr}Syntax error{/tr}</span><br />{/if}
<input type="text" name="start_freeform" value="{$start_freeform}" />
<a {popup text="{tr}Format: mm/dd/yyyy hh:mm<br />...{/tr} {tr}See strtotime php function{/tr}"}><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
{tr}or{/tr}
{/if}
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
{if $feature_cal_manual_time eq 'y'}
{if $end_freeform_error eq 'y'}<span class="attention">{tr}Syntax error{/tr}</span><br />{/if}
<input type="text" name="end_freeform" value="{$end_freeform}" />
<a {popup text="{tr}Format: mm/dd/yyy hh:mm<br />...{/tr} {tr}See strtotime php function{/tr}"}><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
 {tr}or{/tr}
{/if}
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
<span  style="float:right;"><a href="tiki-calendar.php?calitemId={$calitemId}&amp;delete=1"  title="{tr}remove{/tr}"><img src="img/icons2/delete.gif" border="0" width="16" height="16" alt="{tr}remove{/tr}" /></a></span>
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

{* 
   ############################################################
              ----- FORM END
   ############################################################
*}

{/if}{/if} {* modifTab *}