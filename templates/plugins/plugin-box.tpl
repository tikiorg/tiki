<table {if $plugin_box_align} align="{$plugin_box_align}" {/if} {if $plugin_box_width} width="{$plugin_box_width}" {/if}>
<tr><td  {if $plugin_box_width} width="{$plugin_box_width}" {/if}>
<div class='cbox' {if $plugin_box_bg} style="background-color: {$plugin_box_bg}" {/if}>
	<div class='cbox-title'>{$plugin_box_title}</div>
	<div class='cbox-data' {if $plugin_box_bg} style="background-color: {$plugin_box_bg}" {/if}>
	{$plugin_box_data}
	</div>
</div>
</td></tr>
</table>


