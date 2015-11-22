{* $Id$ *}
{if isset($tstotals)}
	{if empty($nofoot)}
		<tfoot class="tablesorter-totals">
	{/if}
	{if isset($tstotals.coltotal)}
		{foreach $tstotals.coltotal as $info}
			<tr>
				{if !empty($precols)}
					{for $i=1 to $precols}
						<th>
							{if $i === 1}
								{if isset($info.label)}
									{$info.label|escape}
								{else}
									{tr}Totals{/tr}
								{/if}
							{/if}
						</th>
					{/for}
				{else}
					{$precols = 0}
				{/if}
				{for $i=1 to $fieldcount}
					{$index = $i - 1 + $precols}
					{if in_array($index, $tstotals.ignore)}
						{$ignore = 1}
					{else}
						{$ignore = 0}
					{/if}
					<th {if !empty($info.type) && !$ignore}data-tsmath="col-{$info.type|escape}" class="text-right"{/if}>
						{if $i === 1 && $ignore && empty($precols)}
							{if isset($info.label)}
								{$info.label|escape}
							{else}
								{tr}Totals{/tr}
							{/if}
						{/if}
					</th>
				{/for}
				{if !empty($postcols)}
					{for $i=1 to $postcols}
						<th></th>
					{/for}
				{else}
					{$postcols = 0}
				{/if}
			</tr>

		{/foreach}
	{/if}
	{if isset($tstotals.tabletotal)}
		{foreach $tstotals.tabletotal as $tableinfo}
			<tr>
				{$allcols = $precols + $fieldcount + $postcols}
				{for $i=1 to $allcols}
					<th {if $i == $allcols && !empty($tableinfo.type)}data-tsmath="all-{$tableinfo.type}" class="text-right"{/if}>
						{if $i === 1}
							{if isset($tableinfo.label)}
								{$tableinfo.label|escape}
							{else}
								{tr}Table total{/tr}
							{/if}
						{/if}
					</th>
				{/for}
			</tr>
		{/foreach}
	{/if}
	{if empty($nofoot)}
		</tfoot>
	{/if}
{/if}