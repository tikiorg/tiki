<div class="list_filter">
	<form action="{$smarty.server.PHP_SELF}?{query}" method="post">
		<table class="table">
		{foreach from=$filterFields item=field}
		<tr>
			<td class="list_filter_label">
				<label>{$field.name|tr_if}</label>
			</td>
			<td class="list_filter_input tracker_field{$field.fieldId}">
				{if array_key_exists('renderedInput', $field)}
					{$field.renderedInput}
				{else}
					<input type="text" name="ins_{$field.name}" value="{$field.value|escape}" class="form-control">
				{/if}
			</td>
		</tr>
		{/foreach}
		<tr>
			<td>&nbsp;</td>
			<td>
				<input class="button submit btn btn-default" type="submit" name="filter" value="{tr}Filter{/tr}">
				<input class="button submit btn btn-default" type="reset" name="reset_filter" value="{tr}Reset{/tr}">
			</td>
		</tr>
		</table>
	</form>
</div>

{jq}
$('input[name=reset_filter]').off('click').on('click', function() {
	window.location.href = $(this).closest('form').attr('action');
});
{/jq}