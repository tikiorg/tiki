{* $Id: wikiplugin_trackerstat.tpl 12245 2008-03-30 13:38:13Z luciash $ *}
<table class="normal">
<tr class="heading"><td colspan="2">{tr}Upcoming events{/tr}</td></tr>
{cycle values="odd,even" print=false}
{foreach from=$events item=event}
	<tr class="{cycle advance=false}">
		<td>
			{if $datetime eq 1}
				{capture name="start"}{$event.start|tiki_short_datetime}{/capture}
				{capture name="end"}{$event.end|tiki_short_datetime}{/capture}
				{$smarty.capture.start}{if $smarty.capture.start ne $smarty.capture.end}<br />{$smarty.capture.end}{/if}
			{else}
				{capture name="start"}{$event.start|tiki_short_date}{/capture}
				{capture name="end"}{$event.end|tiki_short_date}{/capture}
				{$smarty.capture.start}{if $smarty.capture.start ne $smarty.capture.end}<br />{$smarty.capture.end}{/if}
			{/if}
		</td>
		<td width="100%">
			<a class="linkmodule" href="tiki-calendar_edit_item.php?viewcalitemId={$event.calitemId}">{$event.name|escape}</a>
			{if $desc}<br />{$event.description|escape}{/if}
		</td>
	</tr><!-- {cycle} -->
{/foreach}
</table>