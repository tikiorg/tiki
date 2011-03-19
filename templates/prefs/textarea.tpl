<div class="adminoptionbox clearfix{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}" style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
	{include file="prefs/shared-flags.tpl"}
	<textarea name="{$p.preference|escape}" id="{$p.id|escape}" style="width:95%"{if $p.size} rows="{$p.size|escape}"{/if} {$p.params}>
		{$p.value|escape}
	</textarea>
	{if $p.hint}
		<br/><em>{$p.hint|simplewiki}</em>
	{/if}
	{include file="prefs/shared-dependencies.tpl"}
</div>
