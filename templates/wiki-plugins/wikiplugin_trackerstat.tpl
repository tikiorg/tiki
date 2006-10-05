{* $Header: /cvsroot/tikiwiki/tiki/templates/wiki-plugins/wikiplugin_trackerstat.tpl,v 1.2 2006-10-05 14:21:17 sylvieg Exp $ *}
<table class="normal">
{cycle values="even,odd" print=false}
{section name=istat loop=$stats}
<tr><th class="heading" colspan="{if $show_bar and $show_percent}4{elseif $show_bar or $show_percent}3{else}2{/if}">{$stats[istat].name|escape}</td>
</tr>
{foreach from=$stats[istat].values item=val}
<tr>
<td class="{cycle advance=false}">{$val.count}</td>
<td class="{cycle advance=false}{if $val.me} highlight{/if}">{$val.value|escape}</td>
{if $show_percent}<td class="{cycle advance=false}">%{$val.average|string_format:"%.2f"}</td>{/if}
{if $show_bar}<td class="{cycle advance=false}"><img src="img/leftbar.gif" alt="&lt;" /><img alt="-" src="img/mainbar.gif" height="14" width="{$val.average}" /><img src="img/rightbar.gif" alt="&gt;" /></td>{/if}
<!-- {cycle} -->
</tr>
{/foreach}
{/section}
</table>
