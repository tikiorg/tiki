{foreach $control.options as $key => $label}
	<label class="checkbox-inline">
		<input type="checkbox" id="{$control.name|escape}-{$key|escape}" name="{$control.field|escape}[]" value="{$key|escape}"
			{if $control.values[$key]}checked{/if}>
		{$label|escape}
	</label>
{/foreach}
