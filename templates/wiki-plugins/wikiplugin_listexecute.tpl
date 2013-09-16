<form method="post" action="">
	<ol>
		{foreach from=$results item=entry}
			<li>
				{if $entry.report_status eq 'none'}
					<input type="checkbox" name="objects[]" value="{$entry.object_type|escape}:{$entry.object_id|escape}">
				{elseif $entry.report_status eq 'success'}
					{icon _id=accept}
				{else}
					{icon _id=sticky alt="{tr}Error{/tr}"}
				{/if}
				{object_link type=$entry.object_type id=$entry.object_id}
			</li>
		{/foreach}
	</ol>
	<select name="list_action">
		<option></option>
		{foreach from=$actions item=action}
			<option value="{$action|escape}">{$action|escape}</option>
		{/foreach}
	</select>
	<input type="submit" class="btn btn-default" value="{tr}Apply{/tr}">
</form>
