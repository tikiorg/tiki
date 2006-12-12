{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-calendar.tpl,v 1.70 2006-12-12 23:34:48 mose Exp $ *}
{popup_init src="lib/overlib.js"}
<div id="calscreen">

<div style="float:right;margin:5px;">
{if $tiki_p_admin_calendar eq 'y' or $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-admin_calendars.php" class="linkbut">{tr}admin{/tr}</a></span>
<span class="button2"><a href="tiki-admin.php?page=calendar" class="linkbut">{tr}configure{/tr}</a></span>
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

<h1><a class="pagetitle" href="tiki-calendar.php">{tr}Calendar{/tr}</a></h1>

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
