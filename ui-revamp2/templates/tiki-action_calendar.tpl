{popup_init src="lib/overlib.js"}
<div id="calscreen">

<div style="float:right;margin:5px;">
	{if $viewlist neq 'list'}
		{if $group_by_item neq 'n'}
			{button href="?gbi=n" _text=" {tr}Do not group by item{/tr}"}
		{else}
			{button href="?gbi=y" _text=" {tr}Group by item{/tr}"}
		{/if}
	{/if}

	{button href="#" _onclick="toggle('filtercal');" _text="{tr}Filter{/tr}"}

	{if $viewlist eq 'list'}
		{button href="?viewlist=table" _text="{tr}Calendar View{/tr}"}
	{else}
		{button href="?viewlist=list" _text="{tr}List View{/tr}"}
	{/if}
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

