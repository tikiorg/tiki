{* $Id$ *}
{tabset name='stat' toggle='n'}
	{foreach from=$stat key=when item=typeStat}
		{capture name='tabtitle'}
			{if $when eq 'lastday'}{tr}Last Day{/tr}
			{elseif $when eq 'day'}{tr}Day{/tr}
			{elseif $when eq 'lastweek'}{tr}Last Week{/tr}
			{elseif $when eq 'week'}{tr}Week{/tr}
			{elseif $when eq 'lastmonth'}{tr}Last Month{/tr}
			{elseif $when eq 'month'}{tr}Month{/tr}
			{elseif $when eq 'lastyear'}{tr}Last Year{/tr}
			{elseif $when eq 'year'}{tr}Year{/tr}{/if}
		{/capture}	
		{tab name=$smarty.capture.tabtitle}
			 <ul>
			{foreach from=$typeStat key=type item=list}
				{foreach from=$list key=what item=nb} 
					<li>{$what}: {$nb}</li>
				{/foreach}
			{/foreach}
			</ul>
		{/tab}
	{/foreach}
{/tabset}
