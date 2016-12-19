<a name="listexecute_{$iListExecute}"></a>
<form method="post" action="#listexecute_{$iListExecute}" class="form-inline" id="listexecute-{$iListExecute}">
	<button class="listexecute-select-all btn btn-default btn-sm">{tr}Select All{/tr}</button>
	<ol>
		{foreach from=$results item=entry}
			<li>
				<input type="checkbox" name="objects[]" value="{$entry.object_type|escape}:{$entry.object_id|escape}">
				{if $entry.report_status eq 'success'}
					{icon name='ok'}
				{elseif $entry.report_status eq 'error'}
					{icon name='error'}
				{/if}
				{object_link type=$entry.object_type id=$entry.object_id backuptitle=$entry.title}
			</li>
		{/foreach}
	</ol>
	<select name="list_action">
		<option></option>
		{foreach from=$actions item=action}
			<option value="{$action->getName()|escape}" data-input="{$action->requiresInput()}">{$action->getName()|escape}</option>
		{/foreach}
	</select>
	<input type="text" name="list_input" value="" class="form-control" style="display:none">
	<input type="submit" class="btn btn-default btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
</form>
{jq}
$('.listexecute-select-all').removeClass('listexecute-select-all').on('click', function (e) {
	$(this).closest('form').find(':checkbox:not(:checked):not(:disabled)').prop('checked', true);
	e.preventDefault();
});
$('#listexecute-{{$iListExecute}}').find('select[name=list_action]').on('change', function() {
	if( $(this).find('option:selected').data('input') ) {
		$(this).siblings('input[name=list_input]').show();
	} else {
		$(this).siblings('input[name=list_input]').hide();
	}
});
{/jq}
