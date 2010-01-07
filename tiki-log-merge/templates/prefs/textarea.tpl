<div style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
	<textarea name="{$p.preference|escape}" id="{$p.id|escape}" style="width:95%"{if $p.size} rows="{$p.size|escape}"{/if}>{$p.value|escape}</textarea>
	{include file=prefs/shared-flags.tpl}
	{if $p.hint}
		<br/><em>{$p.hint|escape}</em>
	{/if}
	{include file=prefs/shared-dependencies.tpl}
</div>
