{* $Id$ *}
<table class="normal wikiplugin_trackerstat">
{cycle values="even,odd" print=false}
{section name=istat loop=$stats}
<tr><th class="heading" colspan="{if $show_bar eq 'y' and $show_percent eq 'y'}4{elseif $show_bar eq 'y' or $show_percent eq 'y'}3{else}2{/if}">{$stats[istat].name|escape}</th>
</tr>
{foreach from=$stats[istat].values item=val}
<tr>
<td class="{cycle advance=false}">{$val.count}</td>
<td class="{cycle advance=false}{if $val.me} highlight{/if}">{$val.value|escape}</td>
{if $show_percent eq 'y'}<td class="{cycle advance=false}">%{$val.average|string_format:"%.2f"}</td>{/if}
{if $show_bar eq 'y'}<td class="{cycle advance=false}"><img src="img/leftbar.gif" alt="&lt;" /><img alt="-" src="img/mainbar.gif" height="14" width="{$val.average}" /><img src="img/rightbar.gif" alt="&gt;" /></td>{/if}
<!-- {cycle} -->
</tr>
{/foreach}
{/section}
</table>
