<a name="listexecute_{$iListExecute}"></a>
{if $errors}
	{remarksbox type="errors" title="Errors"}
		{foreach from=$errors item=error}
			{$error}<br/>
		{/foreach}
	{/remarksbox}
{/if}
<form method="post" action="#listexecute_{$iListExecute}">
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
