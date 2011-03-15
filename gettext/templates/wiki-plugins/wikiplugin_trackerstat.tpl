<table class="normal wikiplugin_trackerstat">
{cycle values="even,odd" print=false}
{section name=istat loop=$stats}
<tr><th colspan="{if $show_bar eq 'y' and $show_percent eq 'y'}4{elseif $show_bar eq 'y' or $show_percent eq 'y'}3{else}2{/if}">{$stats[istat].name|escape}</th>
</tr>
{foreach from=$stats[istat].values item=val}
<tr>
<td class="{cycle advance=false}">{$val.count}</td>
<td class="{cycle advance=false}{if $val.me} highlight{/if}">
	{if $show_link eq 'y'}<a href="tiki-view_tracker.php?{$val.href}">{/if}
	{$val.value|escape}
	{if $show_link eq 'y'}</a>{/if}
</td>
{if $show_percent eq 'y'}<td class="{cycle advance=false}">%{$val.average|string_format:"%.2f"}</td>{/if}
{if $show_bar eq 'y'}<td class="{cycle advance=false}">{quotabar length=$val.average}</td>{/if}
<!-- {cycle} -->
</tr>
{/foreach}
{/section}
</table>
