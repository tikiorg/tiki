{title help="Server+Compatibility"}{tr}Server Compatibility{/tr}{/title}

<h2>{tr}PHP{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Tiki Fitness{/tr}</th>
		<th>{tr}Explanation{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{foreach from=$php_properties key=key item=item}
		<tr class="{cycle}">
			<td class="text">{$key}</td>
			<td class="text">{$item.setting}</td>
			<td class="text">
				{if $item.fitness eq 'good'}
					{icon _id=accept alt="$item.fitness" style="vertical-align:middle"}
				{elseif $item.fitness eq 'bad'}
					{icon _id=exclamation alt="$item.fitness" style="vertical-align:middle"}
				{elseif $item.fitness eq 'ugly'}
					{icon _id=error alt="$item.fitness" style="vertical-align:middle"}
				{elseif $item.fitness eq 'unknown'}
					{icon _id=error alt="$item.fitness" style="vertical-align:middle"}
				{/if}
				{$item.fitness}
			</td>
			<td class="text">{$item.message}</td>
		</tr>
	{/foreach}
	{if !$php_properties}
         {norecords _colspan=4}
	{/if}
</table>

<h2>{tr}MySQL{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Tiki Fitness{/tr}</th>
		<th>{tr}Explanation{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{foreach from=$mysql_properties key=key item=item}
		<tr class="{cycle}">
			<td class="text">{$key}</td>
			<td class="text">{$item.setting}</td>
			<td class="text">
				{if $item.fitness eq 'good'}
					{icon _id=accept alt="$item.fitness" style="vertical-align:middle"}
				{elseif $item.fitness eq 'bad'}
					{icon _id=exclamation alt="$item.fitness" style="vertical-align:middle"}
				{elseif $item.fitness eq 'ugly'}
					{icon _id=error alt="$item.fitness" style="vertical-align:middle"}
				{elseif $item.fitness eq 'unknown'}
					{icon _id=error alt="$item.fitness" style="vertical-align:middle"}
				{/if}
				{$item.fitness}
			</td>
			<td class="text">{$item.message}</td>
		</tr>
	{/foreach}
	{if !$mysql_properties}
         {norecords _colspan=4}
	{/if}
</table>
