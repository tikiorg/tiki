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
	<td class="heading">{tr}#{/tr}</td>
	<td class="heading">{tr}prev{/tr}</td>
	<td class="heading">{tr}item{/tr}</td>
	<td class="heading">{tr}chg{/tr}</td>
	{if $chart_info.showVotes eq 'y'}
	<td class="heading">{tr}votes{/tr}</td>
	{/if}
	{if $chart_info.showAverage eq 'y'}
	<td class="heading">{tr}avg{/tr}</td>
	{/if}
	<td class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}">{$items[ix].position}</td>
	<td class="{cycle advance=false}">{tr}prev{/tr}</td>
	<td class="{cycle advance=false}">{tr}item{/tr}</td>
	<td class="{cycle advance=false}">{tr}chg{/tr}</td>
	{if $chart_info.showVotes eq 'y'}
	<td class="{cycle advance=false}">{tr}votes{/tr}</td>
	{/if}
	{if $chart_info.showAverage eq 'y'}
	<td class="{cycle advance=false}">{tr}avg{/tr}</td>
	{/if}
	<td class="{cycle advance=false}">&nbsp;</td>
</tr>	
{sectionelse}

{/section}
</table>
