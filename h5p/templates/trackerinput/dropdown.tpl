{strip}
	{if $field.type eq 'R'}
		<div class="input-group">
		{foreach from=$field.possibilities key=value item=label}
			<label class="radio-inline">
				<input type="radio" name="{$field.ins_id|escape}" value="{$value|escape}" {if $field.value eq "$value"}checked="checked"{/if}>
				{$label|tr_if|escape}
			</label>
		{/foreach}
		</div>
	{elseif $field.type eq 'M'}
		{if empty($field.options_map.inputtype)}
			<div class="input-group">
				{foreach from=$field.possibilities key=value item=label}
					<label class="checkbox-inline">
						<input type="checkbox" name="{$field.ins_id|escape}[]" value="{$value|escape}" {if in_array("$value", $field.selected)}checked="checked"{/if}>
						{$label|tr_if|escape}
					</label>
				{/foreach}
			</div>
		{elseif $field.options_map.inputtype eq 'm'}
			{if $prefs.jquery_ui_chosen neq 'y'}<small>{tr}Hold "Ctrl" in order to select multiple values{/tr}</small><br>{/if}
			<select name="{$field.ins_id}[]" multiple="multiple" class="form-control">
				{foreach key=ku from=$field.possibilities key=value item=label}
					<option value="{$value|escape}" {if in_array("$value", $field.selected)}selected="selected"{/if}>{$label|escape}</option>
				{/foreach}
			</select>
		{/if}
		<input type="hidden" name="{$field.ins_id}_old" value="{$field.value|escape}">
	{else}
		<select name="{$field.ins_id|escape}" class="form-control{if $field.type eq 'D'} group_{$field.ins_id|escape}{/if}">
			{assign var=otherValue value=$field.value}
			{if $field.isMandatory ne 'y' || empty($field.value)}
				<option value="">&nbsp;</option>
			{/if}
			{foreach from=$field.possibilities key=value item=label}
				<option value="{$value|escape}"
				{if (isset($field.value) && $field.value ne '') && ($field.value eq "$value")}selected="selected"{/if}>
					{$label|tr_if|escape}
				</option>
			{/foreach}
		</select>

		{if $field.type eq 'D'}
			<div class="col-md-offset-1">
				<label{if !isset($field.possibilities[$field.value]) && $field.value} style="display:inherit;"{else} style="display:none;"{/if}>
					{tr}Other:{/tr}
					<input type="text" class="group_{$field.ins_id|escape} form-control" name="other_{$field.ins_id}" value="{if !isset($field.possibilities[$field.value])}{$field.value|escape}{/if}">
				</label>
			</div>
			{jq}
			$(function () {
				var $select = $('select[name="{{$field.ins_id|escape}}"]'),
					$other = $('input[name="other_{{$field.ins_id|escape}}"]');
				{{if !isset($field.possibilities[$field.value]) && $field.value}}
				if (!$('> [selected]', $select).length) {
					$select.val('{tr}other{/tr}').trigger('chosen:updated');
				}
				{{/if}}
				$select.change(function() {
					if ($select.val() != '{tr}other{/tr}') {
						$other.data('tiki_never_visited', '');
						$other.val('').parent().hide();
					} else {
						$other.data('tiki_never_visited', 'tiki_never_visited');
						$other.parent().show();
					}
				});
				$other.change(function(){
					$other.data('tiki_never_visited', '');
					if ($(this).val()) {
						$select.val(tr('other')).trigger('chosen:updated');
					}
				});
				$other.focusout(function(){
					$other.data('tiki_never_visited', '');
				});
			});
			{/jq}
		{/if}

	{/if}
{/strip}
