<div style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
	<select name="{$p.preference|escape}[]" id="{$p.id|escape}" multiple="multiple">
		{foreach from=$p.options key=value item=label}
			<option value="{$value|escape}"{if in_array($value, $p.value)} selected="selected"{/if}>{$label|escape}</option>
		{/foreach}
	</select>
	{include file=prefs/shared-flags.tpl}
	{if $p.hint}
		<br/><em>{$p.hint|escape}</em>
	{/if}
	{include file=prefs/shared-dependencies.tpl}
	<br /><em>{tr}Use Ctrl+Click to select multiple options{/tr}.</em>
</div>
