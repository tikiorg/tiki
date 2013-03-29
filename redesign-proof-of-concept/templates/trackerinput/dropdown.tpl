{strip}
{if $field.type eq 'R'}
	{foreach from=$field.possibilities key=value item=label}
		<label>
			<input type="radio" name="{$field.ins_id|escape}" value="{$value|escape}" {if $field.value eq $value}checked="checked"{/if}>
			{$label|tr_if|escape}
		</label>
	{/foreach}
{elseif $field.type eq 'M'}
	{foreach from=$field.possibilities key=value item=label}
		<label>
			<input type="checkbox" name="{$field.ins_id|escape}[]" value="{$value|escape}" {if in_array($value, $field.selected)}checked="checked"{/if}>
			{$label|tr_if|escape}
		</label>
	{/foreach}
{else}
	<select name="{$field.ins_id|escape}"{if $field.type eq 'D'} class="group_{$field.ins_id|escape}"{/if}>
		{assign var=otherValue value=$field.value}
		{if $field.isMandatory ne 'y' || empty($field.value)}
			<option value="">&nbsp;</option>
		{/if}
		{foreach from=$field.possibilities key=value item=label}
			<option value="{$value|escape}" 
			{if (isset($field.value) && $field.value ne '') && ($field.value eq $value)}selected="selected"{/if}>
				{$label|tr_if|escape}
			</option>
		{/foreach}
	</select>

	{if $field.type eq 'D'}
		<br>
		<label>
			{tr}Other:{/tr}
			<input type="text" class="group_{$field.ins_id|escape}" name="other_{$field.ins_id}" value="{if !isset($field.possibilities[$field.value])}{$field.value|escape}{/if}">
		</label>
		{if !isset($field.possibilities[$field.value]) && $field.value}
			{jq}
			if (!$('select[name="{{$field.ins_id|escape}}"] > [selected]').length) {
				$('select[name="{{$field.ins_id|escape}}"]').val('other');
			}
			$('select[name="{{$field.ins_id|escape}}"]').change(function() {
				if ($('select[name="{{$field.ins_id|escape}}"]').val() != 'other') {
					$('input[name="other_{{$field.ins_id|escape}}"]').val('');
				}
			});
			{/jq}
		{/if}
	{/if}

{/if}
{/strip}
