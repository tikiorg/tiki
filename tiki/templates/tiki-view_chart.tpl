<a class="pagetitle" href="tiki-view_chart.php?chartId={$smarty.request.chartId}">{$chart_info.title}</a>
<br/><br/>
{if $prevPeriod > 0}
Prev
{/if}
{if $nextPeriod > 0}
Next
{/if}
<table class="normal">
<tr>
	<td style="text-align:right;" width="2%" class="heading">{tr}pos{/tr}</td>
	<td style="text-align:right;" width="2%" class="heading">{tr}pre{/tr}</td>
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
	<td class="{cycle advance=false}"><a class="link" href="{$items[ix].URL}">{$items[ix].title}</a></td>
	<td style="text-align:right;" class="{cycle advance=false}">
	{if $items[ix].dif eq 'new'}
	    <img src='img/icons/new.gif' border='0' alt='{tr}new{/tr}' />	
	{else}
		{$items[ix].dif}
	{/if}
	
	</td>
	{if $chart_info.showVotes eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$items[ix].votes}</td>
	{/if}
	{if $chart_info.showAverage eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$items[ix].average}</td>
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
