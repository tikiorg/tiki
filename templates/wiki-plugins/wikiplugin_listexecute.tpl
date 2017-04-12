<form method="post" action="">
	<button class="listexecute-select-all">{tr}Select All{/tr}</button>
	<ol>
		{foreach from=$results item=entry}
			<li>
				{if $entry.report_status eq 'none'}
					<input type="checkbox" name="objects[]" value="{$entry.object_type|escape}:{$entry.object_id|escape}">
				{elseif $entry.report_status eq 'success'}
					{icon name='ok'}
				{else}
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
$('.listexecute-select-all').removeClass('listexecute-select-all')
	.on('click', function (e) {
		$(this).closest('form').find(':checkbox:not(:checked)').click();
		e.preventDefault();
	});
{/jq}
