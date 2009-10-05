<div style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
	<input name="{$p.preference|escape}" id="{$p.id|escape}" value="{$p.value|escape}" size="{$p.size|default:80|escape}" type="text" />
	{include file=prefs/shared-flags.tpl}
	{if $p.hint}
		<br/><em>{$p.hint|escape}</em>
	{/if}
	{include file=prefs/shared-dependencies.tpl}
</div>
