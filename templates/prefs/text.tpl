<div class="adminoptionbox">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
	{if is_array( $p.value )}
		<input name="{$p.preference|escape}" id="{$p.id|escape}" value="{$p.value|@implode:$p.separator|escape}" size="{if !empty($p.size)}{$p.size|escape}{else}40{/if}" type="text" />
	{else}
		<input name="{$p.preference|escape}" id="{$p.id|escape}" value="{$p.value|escape}" size="{if !empty($p.size)}{$p.size|escape}{else}40{/if}" type="text" />
	{/if}
	{if !empty($p.detail)}{$p.detail|escape}{/if}
	{include file=prefs/shared-flags.tpl}
	{if !empty($p.shorthint)}
		<em>{$p.shorthint|escape}</em>
	{/if}
	{if !empty($p.hint)}
		<br/><em>{$p.hint|escape}</em>
	{/if}
	{include file=prefs/shared-dependencies.tpl}
</div>
