{* $Id$ *}
<table class="table">
	<thead>
	{$header=false}
	{foreach from=$column item=col}
		{if !empty($col.label) or !empty($col.sort)}
			{$header=true}
			{break}
		{/if}
	{/foreach}
	{if $header}
		<tr>
			{foreach from=$column item=col}
				<th>
					{if $col.sort}
						{self_link _sort_arg=$sort_arg _sort_field=$col.sort}{$col.label|escape}{/self_link}
					{else}
						{$col.label|escape}
					{/if}
				</th>
			{/foreach}
		</tr>
	{/if}
	</thead>
	<tbody>
	{foreach from=$results item=row}
		<tr>
			{foreach from=$column item=col}
				{if $col.mode eq 'raw'}
					<td>{$row[$col.field]}</td>
				{else}
					<td>{$row[$col.field]|escape}</td>
				{/if}
			{/foreach}
		</tr>
	{/foreach}
	</tbody>
</table>
