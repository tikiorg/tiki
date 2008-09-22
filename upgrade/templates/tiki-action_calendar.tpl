{popup_init src="lib/overlib.js"}
<div id="calscreen">

<div style="float:right;margin:5px;">

{if $viewlist neq 'list'}
<span class="button2">
{if $group_by_item neq 'n'}
<a href="{$myurl}?gbi=n" title="{tr}Do not group by item{/tr}">{tr}Do not group by item{/tr}</a>{else}
<a href="{$myurl}?gbi=y" title="{tr}Group by item{/tr}">{tr}Group by item{/tr}</a>{/if}
</span>
{/if}

<span class="button2"><a href="#" onclick="toggle('filtercal');">{tr}Filter{/tr}</a></span>

<span class="button2">
{if $viewlist eq 'list'}
<a href="{$myurl}?viewlist=table" title="{tr}Calendar View{/tr}">{tr}Calendar View{/tr}</a>{else}
<a href="{$myurl}?viewlist=list" title="{tr}List View{/tr}">{tr}List View{/tr}</a>{/if}
</span>

</div>

{title}{tr}Tiki Action Calendar{/tr}{/title}

<form id="filtercal" method="get" action="{$myurl}" name="f" style="display:none;">
<div class="caltitle">{tr}Tools Calendars{/tr}</div>
<div class="caltoggle"><input name="tikiswitch" id="tikiswitch" type="checkbox" onclick="switchCheckboxes(this.form,'tikicals[]',this.checked);" /> <label for="tikiswitch">{tr}Check / Uncheck All{/tr}</label></div>
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

