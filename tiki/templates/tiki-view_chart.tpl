<h1><a class="pagetitle" href="tiki-view_chart.php?chartId={$smarty.request.chartId}">{$chart_info.title}</a></h1>
{if $chart_info.hits > 0}
<small>{tr}viewed{/tr} {$chart_info.hits} {tr}times{/tr}</small>
<br /><br />
{/if}
{if strlen($chart_info.description)}
{$chart_info.description}<br /><br /><br />
{/if}
{if $tiki_p_admin_charts eq 'y'}
<a href="tiki-admin_charts.php?chartId={$smarty.request.chartId}">{icon _id='wrench' alt='{tr}Edit Chart{/tr}'}</a>
{/if}
{if $tiki_p_admin_charts eq 'y' or $tiki_p_suggest_chart_item eq 'y'}
<a href="tiki-admin_chart_items.php?chartId={$smarty.request.chartId}">{icon _id='text_list_numbers' alt='{tr}Edit Items{/tr}'}</a>
{/if}
<a href="tiki-charts.php">{icon _id='table' alt='{tr}List Charts{/tr}'}</a>
<a class="link" href="tiki-view_chart.php?chartId={$smarty.request.chartId}">{icon _id='chart_line' alt='{tr}Last Chart{/tr}'}</a>
{if $chart_info.frequency > 0}
    <br />
	{if $prevPeriod > 0}
	<a href="tiki-view_chart.php?chartId={$smarty.request.chartId}&amp;period={$prevPeriod}">{icon _id='resultset_previous' alt='{tr}previous chart{/tr}'}</a>
	{/if}
	<b>{tr}Chart created{/tr}: {$items[0].timestamp|tiki_long_datetime}</b>
	{if $nextPeriod > 0}
	<a href="tiki-view_chart.php?chartId={$smarty.request.chartId}&amp;period={$nextPeriod}">{icon _id='resultset_next' alt='{tr}next chart{/tr}'}</a>
	{/if}
{/if}
<table class="normal">
<tr>
	<td style="text-align:right;"  class="heading">{tr}pos{/tr}</td>
	<td style="text-align:right;"  class="heading">{tr}pre{/tr}</td>
	<td style="text-align:right;"  class="heading">{tr}permanency{/tr}</td>
	<td class="heading">{tr}Item{/tr}</td>
	<td style="text-align:right;"  class="heading">{tr}chg{/tr}</td>
	{if $chart_info.showVotes eq 'y'}
	<td style="text-align:right;"  class="heading">{tr}votes{/tr}</td>
	{/if}
	{if $chart_info.showAverage eq 'y'}
	<td style="text-align:right;"  class="heading">{tr}avg{/tr}</td>
	{/if}
	<td style="text-align:right;"  class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td style="text-align:right;" class="{cycle advance=false}">{$items[ix].position}</td>
	<td style="text-align:right;" class="{cycle advance=false}">{$items[ix].lastPosition}</td>
	<td style="text-align:right;" class="{cycle advance=false}">{$items[ix].perm}</td>
	<td class="{cycle advance=false}">
        {if $items[ix].URL ne ''}
                <a class="link" target="_blank" href="{$items[ix].URL}">{$items[ix].title}</a>
        {else}
                {$items[ix].title}

        {/if}
	{if $items[ix].dif ne 'new' and $items[ix].dif eq $max_dif}
		{icon _id='thumb_up' alt='{tr}cool{/tr}'}
	{/if}
	</td>
	<td style="text-align:right;" class="{cycle advance=false}">
	{if $items[ix].dif eq 'new'}
	    {icon _id='new'}	
	{else}
		{if $items[ix].dif eq $max_dif}
			{if $items[ix].dif > 0}+{/if}{$items[ix].dif}
		{else}
			{if $items[ix].dif > 0}+{/if}{$items[ix].dif}
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
		<a class="link" href="tiki-view_chart_item.php?itemId={$items[ix].itemId}">{icon _id='monitor' alt='{tr}info/vote{/tr}'}</a>
	{else}
		<a class="link" href="tiki-view_chart_item.php?itemId={$items[ix].itemId}">{icon _id='monitor' alt='{tr}info/vote{/tr}'}</a>
	{/if}
	</td>
</tr>	
{sectionelse}

{/section}
</table>
{if $chart_info.frequency > 0 }
<small>{tr}Next chart will be generated on{/tr}: {$next_chart|tiki_long_datetime}</small>
{/if}
<hr/>
<h4>{tr}View or vote items not listed in the chart{/tr}</h4>
<table>
<tr>
<td>
<form method="post">
{tr}Find{/tr}: <input size="15" type="text" name="find" value="{$smarty.request.find|escape}" />
<input type="submit" name="findb" value="{tr}Find{/tr}" />
</form>
</td>
<td>
<form id='selit' method="post" action="tiki-view_chart_item.php">
<select name="itemId" onchange="javascript:document.getElementById('selit').submit();">
<option value="">{tr}Select something to vote on{/tr}</option>
{section name=ix loop=$all_items}
<option value="{$all_items[ix].itemId|escape}">{$all_items[ix].title}</option>
{/section}
</select>
</form>
</td>
</tr>
</table>
