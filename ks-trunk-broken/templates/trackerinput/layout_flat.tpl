{foreach from=$fields item=field}
	<label>
		{$field.name|escape}
		{if $field.isMandatory eq 'y'}
			<span class="mandatory_star">*</span>
		{/if}
		{trackerinput field=$field}
		<div class="description">
			{$field.description|escape}
		</div>
	</label>
{/foreach}
