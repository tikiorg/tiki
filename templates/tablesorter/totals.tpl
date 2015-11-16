{* $Id$ *}
<tfoot class="tablesorter-totals">
	<tr>
		{if isset($cols)}
			{foreach $cols as $index => $type}
				<th {if !empty($type) && $type != 'ignore'}data-math="{$type|escape}" style="text-align:right"{/if}>
					{if $index === 0 && $type == 'ignore'}
						{if isset($totals.columnlabel)}
							{$totals.columnlabel|escape}
						{else}
							{tr}Totals{/tr}
						{/if}
					{/if}
				</th>
			{/foreach}
		{/if}
	</tr>
	{if isset($totals.page)}
		<tr>
			{for $i=1 to $count}
				<th {if $i == $count}data-math="all-sum" style="text-align:right"{/if}>
					{if $i === 1}
						{if isset($totals.pagelabel)}
							{$totals.pagelabel|escape}
						{else}
							{tr}Page total{/tr}
						{/if}
					{/if}
				</th>
			{/for}
		</tr>
	{/if}
</tfoot>