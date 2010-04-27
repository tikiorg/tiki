<div class="adminoptionbox" style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
		{foreach from=$p.options key=value item=label}
			<span><input type="checkbox" name="{$p.preference|escape}[]" value="{$value|escape}"{if in_array($value, $p.value)} checked="checked"{/if}>{$label|escape}</span>
		{/foreach}
	{include file=prefs/shared-flags.tpl}
	{if $p.shorthint}
		<em>{$p.shorthint|escape}</em>
	{/if}
	{if $p.hint}
		<br/><em>{$p.hint|escape}</em>
	{/if}
	{include file=prefs/shared-dependencies.tpl}
</div>
