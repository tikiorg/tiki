{* $Id$ *}
{if $actions}
<form method="post" action="#{$id}">
{/if}
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
				{if $actions}
					<th><input type="checkbox" name="selectall" value="" class="listexecute-select-all"></th>
				{/if}
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
				{if $actions}
					<td>
						<input type="checkbox" name="objects[]" value="{$row.object_type|escape}:{$row.object_id|escape}">
						{if $row.report_status eq 'success'}
							{icon name='ok'}
						{elseif $row.report_status eq 'error'}
							{icon name='error'}
						{/if}
					</td>
				{/if}
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
		{if !empty($tstotals) && $tsOn}
			{include file="../../tablesorter/totals.tpl" fieldcount="{$fieldcount}"}
		{/if}
	</table>
</div>
{if $actions}
	<select name="list_action">
		<option></option>
		{foreach from=$actions item=action}
			<option value="{$action|escape}">{$action|escape}</option>
		{/foreach}
	</select>
	<input type="submit" class="btn btn-default btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
</form>
{jq}
$('.listexecute-select-all').removeClass('listexecute-select-all').on('click', function (e) {
	if( this.checked ) {
		$(this).closest('form').find(':checkbox:not(:checked):not(:disabled)').click();
	} else {
		$(this).closest('form').find(':checkbox:checked:not(:disabled)').click();
	}
	e.preventDefault();
});
{/jq}
{/if}