{* $Id$ *}
{if isset($tstotals)}
	{$rowtotal = 0}
	{if !empty($tstotals.row)}
		{$rowtotal = $tstotals.row|count}
	{/if}
	{if empty($nofoot)}
		<tfoot class="tablesorter-totals">
	{/if}
	{foreach $tstotals as $key => $value}
		{if $key == 'col'}
			{foreach $value as $info}
				<tr class="ts-foot-row">
					{if !empty($precols)}
						{for $i=1 to $precols}
							<th>
								{if $i === 1}
									{$info.label|escape}
								{/if}
							</th>
						{/for}
					{else}
						{$precols = 0}
					{/if}
					{for $i=1 to $fieldcount}
						{$index = $i - 1 + $precols}
						{if isset($tscols.$index.math.ignore) && $tscols.$index.math.ignore}
							{$ignore = 1}
							{$format = 0}
						{elseif !empty($tscols.$index.math.format)}
							{$ignore = 0}
							{$format = " data-tsmath-mask='{$tscols.$index.math.format}'"}
						{else}
							{$ignore = 0}
							{$format = 0}
						{/if}
						<th {if !empty($info.formula) && !$ignore}data-tsmath="col-{$info.formula|escape}" class="text-right"{if !empty($info.filter)} data-tsmath-filter="{$info.filter}"{/if}{if $format}{$format}{/if}{/if} data-index="{$index}">
							{if $i === 1 && $ignore && empty($precols)}
								{$info.label|escape}
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
					{if $rowtotal}
						{for $i=1 to $rowtotal}
							<th></th>
						{/for}
					{/if}
				</tr>
			{/foreach}
		{/if}
		{if $key == 'all'}
			{foreach $value as $tableinfo}
				<tr class="ts-foot-row">
					{$allcols = $precols + $fieldcount + $postcols + $rowtotal}
					{for $i=1 to $allcols}
						{$index = $i -1}
						{if !empty($tscols.$index.math.format)}
							{$format = " data-tsmath-mask='{$tscols.$index.math.format}'"}
						{else}
							{$format = 0}
						{/if}
						<th {if $i == $allcols && !empty($tableinfo.formula)}data-tsmath="all-{$tableinfo.formula}" class="text-right"{if !empty($tableinfo.filter)} data-tsmath-filter="{$tableinfo.filter}"{/if}{if $format}{$format}{/if}{/if}>
							{if $i === 1}
								{$tableinfo.label|escape}
							{/if}
						</th>
					{/for}
				</tr>
			{/foreach}
		{/if}
	{/foreach}
	{if empty($nofoot)}
		</tfoot>
	{/if}
{/if}