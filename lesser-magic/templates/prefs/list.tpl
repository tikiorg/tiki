<div style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
	<select name="{$p.preference|escape}" id="{$p.id|escape}">
		{foreach from=$p.options key=value item=label}
			<option value="{$value|escape}"{if $value eq $p.value} selected="selected"{/if}>{$label|escape}</option>
		{/foreach}
	</select>
	{include file=prefs/shared-flags.tpl}
	{include file=prefs/shared-dependencies.tpl}
</div>
