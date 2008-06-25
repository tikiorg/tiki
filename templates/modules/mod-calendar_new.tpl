{* $Id: mod-calendar_new.tpl 12242 2008-03-30 13:22:01Z luciash $ *}
{tikimodule title=$module_params.title name=$module_params.name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{popup_init src="lib/overlib.js"}
<div style="text-align:center; font-size:110%">{tr}{$focusdate|tiki_date_format:"%B"|ucfirst}{/tr}</div>
<table cellpadding="0" cellspacing="0" border="0" id="caltable" style="text-align:center;">
<tr>
{section name=dn loop=$daysnames}
<th class="days" width="14%">{$daysnames[dn][0]|ucfirst}</th>
{/section}
</tr>
{cycle values="odd,even" print=false}
{section name=w loop=$cell}
<tr>
{section name=d loop=$weekdays}
{assign var=day_cursor value=$cell[w][d].day|date_format:"%d"}
{assign var=month_cursor value=$cell[w][d].day|date_format:"%m"}
{assign var=day_today value=$smarty.now|date_format:"%d"}
{assign var=month_today value=$smarty.now|date_format:"%m"}

{if $cell[w][d].focus}
{cycle values="calodd,caleven" print=false}
{else}
{cycle values="caldark" print=false}
{/if}
<td class="{if $day_cursor eq $day_today && $month_cursor eq $month_today}calfocuson{else}{cycle advance=false}{/if}" width="14%" style="text-align:center; font-size:0.8em; {if $day_cursor eq $focusday && $month_cursor eq $focusmonth}background-color: #8DF378; border-bottom: 2px solid green;{/if}">

{assign var=over value=$cell[w][d].items[0].over}
{if $month_cursor neq $focusmonth }
<span style="color:lightgrey">{$day_cursor}</span>
{elseif $cell[w][d].items[0].modifiable eq "y" || $cell[w][d].items[0].visible eq 'y'}
<a style="text-decoration: underline; font-weight: bold" href="{$myurl}?todate={$cell[w][d].day}&amp;viewmode=day"
{if $prefs.calendar_sticky_popup eq "y" and $cell[w][d].items[0].calitemId}{popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}{else}{popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}{/if}
>{$day_cursor}</a>
{else}
{$day_cursor}
{/if}

</td>
{/section}
</tr>
{/section}
</table>
{/tikimodule}
