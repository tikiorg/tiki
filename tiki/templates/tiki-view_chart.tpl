<a class="pagetitle" href="tiki-view_chart.php?chartId={$smarty.request.chartId}">{$chart_info.title}</a>
<br/><br/>
{if strlen($chart_info.description)}
{$chart_info.description}<br/><br/><br/>
{/if}
{if $tiki_p_admin_charts eq 'y'}
<a href="tiki-admin_charts.php?chartId={$smarty.request.chartId}"><img src='img/icons/config.gif' border='0' alt='{tr}edit chart{/tr}' title='{tr}edit chart{/tr}' /></a>
<a href="tiki-admin_chart_items.php?chartId={$smarty.request.chartId}"><img src='img/icons/ico_olist.gif' border='0' alt='{tr}edit items{/tr}' title='{tr}edit items{/tr}' /></a>
{/if}
<a href="tiki-charts.php"><img src='img/icons/ico_table.gif' border='0' alt='{tr}list charts{/tr}' title='{tr}list charts{/tr}' /></a>
{if $chart_info.frequency > 0}
    <br/>
	{if $prevPeriod > 0}
	Prev
	{/if}
	{tr}Chart created{/tr}:{$chart_info.lastChart|tiki_long_datetime}
	{if $nextPeriod > 0}
	Next
	{/if}
{/if}
<table class="normal">
<tr>
	<td style="text-align:right;" width="2%" class="heading">{tr}pos{/tr}</td>
	<td style="text-align:right;" width="2%" class="heading">{tr}pre{/tr}</td>
	<td style="text-align:right;" width="2%" class="heading">{tr}perm{/tr}</td>
	<td class="heading">{tr}item{/tr}</td>
	<td style="text-align:right;" width="2%" class="heading">{tr}chg{/tr}</td>
	{if $chart_info.showVotes eq 'y'}
	<td style="text-align:right;" width="2%" class="heading">{tr}votes{/tr}</td>
	{/if}
	{if $chart_info.showAverage eq 'y'}
	<td style="text-align:right;" width="2%" class="heading">{tr}avg{/tr}</td>
	{/if}
	<td style="text-align:right;" width="2%" class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td style="text-align:right;" class="{cycle advance=false}">{$items[ix].position}</td>
	<td style="text-align:right;" class="{cycle advance=false}">{$items[ix].lastPosition}</td>
	<td style="text-align:right;" class="{cycle advance=false}">{$items[ix].perm}</td>
	<td class="{cycle advance=false}"><a class="link" target="_new" href="{$items[ix].URL}">{$items[ix].title}</a>
	{if $items[ix].dif ne 'new' and $items[ix].dif eq $max_dif}
		<img src='img/icons/cool.gif' alt='{tr}cool{/tr}' />
	{/if}
	</td>
	<td style="text-align:right;" class="{cycle advance=false}">
	{if $items[ix].dif eq 'new'}
	    <img src='img/icons/new.gif' border='0' alt='{tr}new{/tr}' />	
	{else}
		{if $items[ix].dif eq $max_dif}
			{$items[ix].dif}!
		{else}
			{$items[ix].dif}
		{/if}
	{/if}
	
	</td>
	{if $chart_info.showVotes eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$items[ix].rvotes}</td>
	{/if}
	{if $chart_info.showAverage eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$items[ix].raverage}</td>
	{/if}
	<td style="text-align:right;" class="{cycle advance=false}">
	{if ($chart_info.singleChartVotes eq 'n' or $user_voted_chart eq 'n')
		and
		($chart_info.singleItemVotes eq 'n' or $items[ix].voted eq 'n') }
		<a class="link" href="tiki-view_chart_item.php?itemId={$items[ix].itemId}"><img src='img/icons/edit.gif' border='0' alt='{tr}info/vote{/tr}' title='{tr}info/vote{/tr}' /></a>
	{else}
		<a class="link" href="tiki-view_chart_item.php?itemId={$items[ix].itemId}"><img src='img/icons/edit.gif' border='0' alt='{tr}info/vote{/tr}' title='{tr}info/vote{/tr}' /></a>
	{/if}
	</td>
</tr>	
{sectionelse}

{/section}
</table>
{if $chart_info.frequency > 0 }
<small>{tr}Next chart will be generated on{/tr}: {$next_chart|tiki_long_datetime}</small>
{/if}