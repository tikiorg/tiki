{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-calendar.tpl,v 1.72 2007-04-07 10:48:26 nyloth Exp $ *}
{popup_init src="lib/overlib.js"}
<div id="calscreen">

<div style="float:right;margin:5px;">
{if $displayedcals|@count eq 1 and $user and $feature_user_watches eq 'y'}
{if $user_watching eq 'y'}
<a href="tiki-calendar.php?watch_event=calendar_changed&amp;watch_action=remove">{html_image file='pics/icons/no_eye.png' border='0' alt="{tr}stop monitoring this page{/tr}" title="{tr}stop monitoring this page{/tr}"}</a>
{else}
<a href="tiki-calendar.php?watch_event=calendar_changed&amp;watch_action=add">{html_image file='pics/icons/eye.png' border='0' alt="{tr}monitor this page{/tr}" title="{tr}monitor this page{/tr}"}</a>
{/if}
{/if}

{if $tiki_p_admin_calendar eq 'y' or $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-admin_calendars.php" class="linkbut">{tr}admin{/tr}</a></span>
<span class="button2"><a href="tiki-admin.php?page=calendar" class="linkbut">{tr}Configure/Options{/tr}</a></span>
{/if}
{if $tiki_p_add_event eq 'y'}
<span class="button2"><a href="tiki-calendar_edit_item.php"class="linkbut">{tr}add item{/tr}</a></span>
{/if}

<span class="button2"><a href="#" title="{tr}Click to select visible calendars{/tr}" class="linkbut" onclick="toggle('filtercal');">{tr}Visible Calendars{/tr}</a></span>

{if count($thiscal)}
{foreach item=k from=$listcals name=listc}
{if $thiscal.$k}
<span class="button2"><a href="#" class="linkbut" style="background-color:#{$infocals.$k.custombgcolor};color:#{$infocals.$k.customfgcolor}" onclick="toggle('filtercal');">{$infocals.$k.name}</a></span>
{/if}
{/foreach}
{else}
<span class="button2" style="background-color:#fff;padding:0 4px;">
none
</span>
{/if}


<span class="button2">
{if $viewlist eq 'list'}
<a href="{$myurl}?viewlist=table" class="linkbut" title="{tr}calendar view{/tr}">{tr}calendar view{/tr}</a>{else}
<a href="{$myurl}?viewlist=list" class="linkbut" title="{tr}list view{/tr}">{tr}list view{/tr}</a>{/if}
</span>

</div>
<h1><a class="pagetitle" href="tiki-calendar.php">{if $displayedcals|@count eq 1}{tr}Calendar:{/tr} {assign var=x value=$displayedcals[0]}{$infocals[$x].name}{else}{tr}Calendar{/tr}{/if}</a></h1>

<form id="filtercal" method="get" action="{$myurl}" name="f" style="display:none;">
<div class="caltitle">{tr}Group Calendars{/tr}</div>
<div class="caltoggle"><input name="calswitch" id="calswitch" type="checkbox" onchange="switchCheckboxes(this.form,'calIds[]',this.checked);"/> <label for="calswitch">{tr}check / uncheck all{/tr}</label></div>
{foreach item=k from=$listcals}
<div class="calcheckbox"><input type="checkbox" name="calIds[]" value="{$k|escape}" id="groupcal_{$k}" {if $thiscal.$k}checked="checked"{/if} />
<label for="groupcal_{$k}" class="calId{$k}">{$infocals.$k.name} (id #{$k})</label>
</div>
{/foreach}
<div class="calinput"><input type="submit" name="refresh" value="{tr}Refresh{/tr}"/></div>
</form>


<div style="margin:5px;">
{include file="tiki-calendar_nav.tpl"}

{if $viewlist eq 'list'}
{include file="tiki-calendar_listmode.tpl"'}

{elseif $viewmode eq 'day'}
{include file="tiki-calendar_daymode.tpl"}

{else}
{include file="tiki-calendar_calmode.tpl"}

{/if}
</div>

</div>

</div>
