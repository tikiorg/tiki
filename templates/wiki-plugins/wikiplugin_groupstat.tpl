{* $id$ *}
<table class="normal">
<tr><th colspan="3">{tr}Groups{/tr}</th></tr>
{cycle values="even,odd" print=false}
{foreach from=$stats item=stat}
	{if $stat.group ne 'Anonymous' and $stat.group ne 'Registered'}
		<tr class="{cycle}">
		<td>{$stat.nb}</td>
		<td>{$stat.group}</td>
		<td>
			{if $params.show_bar eq 'y'}
				{if !empty($stat.percent)}
					<img src="img/leftbar.gif" alt="&lt;" /><img alt="-" src="img/mainbar.gif" height="14" width="{$stat.percent}" /><img src="img/rightbar.gif" alt="&gt;" />
				{/if}
			{else}
				%{$stat.percent|string_format:"%.2f"}
			{/if}
		</td>
		</tr>
	{/if}
{/foreach}
</table>
