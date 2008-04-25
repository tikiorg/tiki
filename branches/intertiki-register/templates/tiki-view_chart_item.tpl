<h1><a class="pagetitle" href="tiki-view_chart_item.php?itemId={$smarty.request.itemId}">{tr}Item information{/tr}</a>
</h1>
{cycle values="odd,even" print=false}
<table class="normal">
<tr>
	<td  class="{cycle advance=false}">{tr}Chart{/tr}</td>
	<td class="{cycle }"><b><a class="link" href="tiki-view_chart.php?chartId={$chart_info.chartId}">{$chart_info.title}</a></b></td>
</tr>
<tr>
	<td  class="{cycle advance=false}">{tr}Item{/tr}</td>
	<td class="{cycle }"><b>{$info.title}</b></td>
</tr>
<tr>
	<td  class="{cycle advance=false}">{tr}Description{/tr}</td>
	<td class="{cycle }">{$info.description}</td>
</tr>
<tr>
	<td  class="{cycle advance=false}">{tr}Permanency{/tr}</td>
	<td class="{cycle }">{$info.perm}</td>
</tr>
<tr>
	<td  class="{cycle advance=false}">{tr}Position{/tr}</td>
	<td class="{cycle }">{$info.position}</td>
</tr>
<tr>
	<td  class="{cycle advance=false}">{tr}Previous{/tr}</td>
	<td class="{cycle }">{$info.lastPosition}</td>
</tr>
<tr>
	<td  class="{cycle advance=false}">{tr}Dif{/tr}</td>
	<td class="{cycle }">{if $info.dif eq "new"}{html_image file='img/icons/new.gif' border='0' alt='{tr}New{/tr}'}{else}{$info.dif}{/if}</td>
</tr>
<tr>
	<td  class="{cycle advance=false}">{tr}Best Position{/tr}</td>
	<td class="{cycle }">{$info.best}</td>
</tr>
{if $chart_info.showVotes eq 'y'}
<tr>
	<td  class="{cycle advance=false}">{tr}Votes{/tr}</td>
	<td class="{cycle }">{$info.votes}</td>
</tr>
{/if}
{if $chart_info.showAverage eq 'y'}
<tr>
	<td  class="{cycle advance=false}">{tr}Average{/tr}</td>
	<td class="{cycle }">{$info.average}</td>
</tr>
{/if}

{if ($tiki_p_admin_charts eq 'y') or
	(($chart_info.singleChartVotes eq 'n' or $user_voted_chart eq 'n')
	and
	($chart_info.singleItemVotes eq 'n' or $user_voted_item eq 'n'))}
<tr>
	<td  class="{cycle advance=false}">{tr}Vote this item{/tr}</td>
	<td class="{cycle}">
		<form method="post">
		<input type="hidden" name="itemId" value="{$info.itemId|escape}" />
		{if $chart_info.maxVoteValue eq '5'}
		{tr}Lowest{/tr}
		<input type="radio" name="points" value="1" />
		<input type="radio" name="points" value="2" />
		<input type="radio" name="points" value="3" />
		<input type="radio" name="points" value="4" />
		<input type="radio" name="points" value="5" />
		{tr}Highest{/tr}
		{elseif $chart_info.maxVoteValue eq '10'}
		{tr}Lowest{/tr}
		<input type="radio" name="points" value="1" />
		<input type="radio" name="points" value="2" />
		<input type="radio" name="points" value="3" />
		<input type="radio" name="points" value="4" />
		<input type="radio" name="points" value="5" />
		<input type="radio" name="points" value="6" />
		<input type="radio" name="points" value="7" />
		<input type="radio" name="points" value="8" />
		<input type="radio" name="points" value="9" />
		<input type="radio" name="points" value="10" />
		{tr}Highest{/tr}
		{/if}
		<input type="submit" name="vote" value="{tr}vote{/tr}" />
		</form>
	</td>
</tr>
{/if}
</table>
