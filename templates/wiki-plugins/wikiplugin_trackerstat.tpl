{* $Id$ *}
<div class="table-responsive">
<table class="table wikiplugin_trackerstat">

{section name=istat loop=$stats}
<tr><th colspan="{if $show_bar eq 'y' and $show_percent eq 'y' and $show_count eq 'y'}4{elseif $show_bar eq 'y' and $show_percent eq 'y' and $show_count eq 'n'}3{elseif $show_bar eq 'y' and $show_percent eq 'n' and $show_count eq 'y'}3{elseif $show_bar eq 'n' and $show_percent eq 'y' and $show_count eq 'y'}3{elseif $show_bar eq 'n' and $show_percent eq 'n' and $show_count eq 'y'}2{elseif $show_bar eq 'n' and $show_percent eq 'y' and $show_count eq 'n'}2{elseif $show_bar eq 'y' and $show_percent eq 'n' and $show_count eq 'n'}2{else}1{/if}">{$stats[istat].name|escape}</th>
</tr>
{foreach from=$stats[istat].values item=val}
<tr>
{if $show_count eq 'y'}<td class="{cycle advance=false}">{$val.count}</td>{/if}
<td class="{cycle advance=false}{if $val.me} highlight{/if}">
	{if $show_link eq 'y'}<a href="tiki-view_tracker.php?{$val.href}">{/if}
	{$val.value}
	{if $show_link eq 'y'}</a>{/if}
</td>
{if $show_percent eq 'y'}<td class="{cycle advance=false}">%{$val.average|string_format:"%.2f"}</td>{/if}
{if $show_bar eq 'y'}<td class="{cycle advance=false}">{quotabar length=$val.average}</td>{/if}
<!-- {cycle} -->
</tr>
{/foreach}
{/section}
</table>
</div>