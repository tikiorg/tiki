{* $Id$ *}
<div {if $id}id="{$id}-div" {/if}class="table-responsive ts-wrapperdiv" {if $tsOn}style="visibility:hidden;"{/if}>
	<table {if $id}id="{$id}" {/if}class="table normal table-hover table-striped" data-count="{$count}">
		<thead>
		{$header=false}
		{foreach from=$column item=col}
			{if !empty($col.label) or !empty($col.sort)}
				{$header=true}
				{break}
			{/if}
		{/foreach}
		{if $header}
			{$fieldcount = 0}
			<tr>
				{foreach from=$column item=col}
					{$fieldcount = $fieldcount + 1}
					<th>
						{if isset($col.sort) && $col.sort}
							{if !empty($sort_jsvar) and !empty($_onclick)}
								{$order = '_asc'}
								{if !empty($smarty.request.sort_mode) and stristr($smarty.request.sort_mode, $col.sort) neq false}
									{if stristr($smarty.request.sort_mode, '_asc')}
										{$order = '_desc'}
									{elseif stristr($smarty.request.sort_mode, '_nasc')}
										{$order = '_ndesc'}
									{elseif stristr($smarty.request.sort_mode, '_desc')}
										{$order = '_asc'}
									{elseif stristr($smarty.request.sort_mode, '_ndesc')}
										{$order = '_nasc'}
									{/if}
								{/if}
								{$click = $sort_jsvar|cat:'=\''|cat:$col.sort|cat:$order|cat:'\';'|cat:$_onclick}
								{self_link _onclick=$click _ajax='y'}{$col.label|escape}{/self_link}
							{else}
								{self_link _sort_arg=$sort_arg _sort_field=$col.sort}{$col.label|escape}{/self_link}
							{/if}
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
					{if isset($col.mode) && $col.mode eq 'raw'}
						<td>{if !empty($row[$col.field])}{$row[$col.field]}{/if}</td>
					{else}
						<td>{if !empty($row[$col.field])}{$row[$col.field]|escape}{/if}</td>
					{/if}
				{/foreach}
			</tr>
		{/foreach}
		</tbody>
		{if $tstotals && $tsOn}
			{include file="../../tablesorter/totals.tpl" fieldcount="{$fieldcount}"}
		{/if}
	</table>
</div>
