{* $Id$ *}
{foreach from=$stat key=type item=typeStat}
	<h3>{$type|escape}</h3>
	<table class="normal">
		<tr>
		{foreach from=$typeStat key=when item=item}
			<th>
				{if $when eq 'lastday'}{tr}Last Day{/tr}
				{elseif $when eq 'day'}{tr}Day{/tr}
				{elseif $when eq 'lastweek'}{tr}Last Week{/tr}
				{elseif $when eq 'week'}{tr}Week{/tr}
				{elseif $when eq 'lastmonth'}{tr}Last Month{/tr}
				{elseif $when eq 'month'}{tr}Month{/tr}
				{elseif $when eq 'lastyear'}{tr}Last Year{/tr}
				{elseif $when eq 'year'}{tr}Year{/tr}{/if}
			</th>
		{/foreach}
		</tr>
		<tr>
		{foreach from=$typeStat item=when}
			 <td style="text-align:center;">{tr}{$when.added}{/tr}</td>
		{/foreach}
		</tr>
	</table>
{/foreach}
