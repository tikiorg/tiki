{popup_init src="lib/overlib.js"}
<div id="calscreen">

<div style="float:right;margin:5px;">
<span class="button2"><a href="#" class="linkbut" onclick="toggle('filtercal');">{tr}filter{/tr}</a></span>

<span class="button2">
{if $viewlist eq 'list'}
<a href="{$myurl}?viewlist=table" class="linkbut" title="{tr}calendar view{/tr}">{tr}calendar view{/tr}</a>{else}
<a href="{$myurl}?viewlist=list" class="linkbut" title="{tr}list view{/tr}">{tr}list view{/tr}</a>{/if}
</span>

</div>

<h1><a class="pagetitle" href="tiki-action_calendar.php">{tr}Tiki Action Calendar{/tr}</a></h1>

<form id="filtercal" method="get" action="{$myurl}" name="f" style="display:none;">
<div class="caltitle">{tr}Tools Calendars{/tr}</div>
<div class="caltoggle"><input name="tikiswitch" id="tikiswitch" type="checkbox" onclick="switchCheckboxes(this.form,'tikicals[]',this.checked);" /> <label for="tikiswitch">{tr}check / uncheck all{/tr}</label></div>
{foreach from=$tikiItems key=ki item=vi}
{if $vi.feature eq 'y' and $vi.right eq 'y'}
<div class="calcheckbox"><input type="checkbox" name="tikicals[]" value="{$ki|escape}" id="tikical_{$ki}" {if in_array($ki,$tikicals)}checked="checked"{/if} />
<label for="tikical_{$ki}" class="Cal{$ki}"> = {$vi.label}</label></div>
{/if}
{/foreach}
<div class="calinput"><input type="submit" name="refresh" value="{tr}Refresh{/tr}"/></div>
</form>

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

