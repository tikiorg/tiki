{* $Id$ *}
{if empty($iListExecute)}{assign var=iListExecute value=$id}{/if}
{if $actions}
<form method="post" action="#{$id}" class="form-inline" id="listexecute-{$iListExecute}">
{/if}
{if not empty($column.field)}
	{$column = [$column]}{* if there is only one column then it will not be in an array *}
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
					{$fieldcount = 1}
					<th>
						<input type="checkbox" name="selectall" value="" class="listexecute-select-all">
						<input type="hidden" name="objects[]" value="" class="listexecute-all">
					</th>
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
						<input type="checkbox" name="objects[]" class="checkbox_objects" value="{$row.object_type|escape}:{$row.object_id|escape}">
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
	<select name="list_action" class="form-control" id="check_submit_select_{$id}">
		<option></option>
		{foreach from=$actions item=action}
			<option value="{$action->getName()|escape}" data-input="{$action->requiresInput()}">{$action->getName()|escape}</option>
		{/foreach}
	</select>
	<input type="text" name="list_input" value="" class="form-control" style="display:none">
	<input type="submit" class="btn btn-default btn-sm" title="{tr}Apply Changes{/tr}" id="submit_form_{$id}" disabled value="{tr}Apply{/tr}">
</form>
{jq}
(function(){
	var countChecked = function() {
		if ($('#{{$id}}-div .checkbox_objects').is(':checked')) {
			if($('select#check_submit_select_{{$id}}').val()){
				$('input#submit_form_{{$id}}').prop('disabled', false);
			}
		} else {
			$('input#submit_form_{{$id}}').prop('disabled', true);
		}
		var header_checked = $('#{{$id}}-div .checkbox_objects').not(':checked').length == 0;
		$('#listexecute-{{$iListExecute}} .listexecute-all').val(header_checked ? 'ALL' : '');
	};
	$('#listexecute-{{$iListExecute}} .listexecute-select-all').removeClass('listexecute-select-all')
		.on('click', function (e) {
			$(this).closest('form').find('tbody :checkbox:not(:disabled)').each(function () {
				$(this).prop("checked", ! $(this).prop("checked"));
			}).promise().done(function(){ countChecked(); });
		});
	$('#listexecute-{{$iListExecute}}').find('select[name=list_action]')
		.on('change', function() {
			var valueSel = $('select#check_submit_select_{{$id}}').val();
			if(valueSel == ''){
				$('input#submit_form_{{$id}}').prop('disabled', true);
			} else {
				if($('#{{$id}}-div .checkbox_objects').is(':checked')){
					$('input#submit_form_{{$id}}').prop('disabled', false);
				}
			}
			if( $(this).find('option:selected').data('input') ) {
				$(this).siblings('input[name=list_input]').show();
			} else {
				$(this).siblings('input[name=list_input]').hide();
			}
		});
	$( "#{{$id}}-div .checkbox_objects" ).on( "click", countChecked );
	countChecked();
})();
{/jq}
{/if}
{if $downloadable}
	{if $actions}
	<br>
	{/if}
	<form method="post" id="listexecute-download-{$iListExecute}">
		<input type="hidden" name="download" value="1">
		<input type="hidden" name="tsAjax" value="y">
		<input type="submit" name="submit" value="{tr}Download{/tr}" class="btn btn-default">
	</form>
{jq}
(function(){
	$('#listexecute-download-{{$iListExecute}}').submit(function(){
		var $form = $(this);
		$form.find('input[name^=filter]').remove();
		$('.tablesorter-filter').each(function(i,el){
			var column = $(el).data('column'),
					value = $(el).val();
			if( value ) {
				$('<input type="hidden" name="filter['+column+']">')
					.val(value)
					.appendTo($form);
			}
		});
	});
})();
{/jq}
{/if}
