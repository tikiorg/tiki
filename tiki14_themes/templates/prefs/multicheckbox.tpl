<div class="adminoptionbox preference clearfix multicheckbox {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}" style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}</label>
	<ul>
		{foreach from=$p.options key=value item=label}
			<li><input type="checkbox" name="{$p.preference|escape}[]" value="{$value|escape}"{if in_array($value, $p.value)} checked="checked"{/if} {$p.params}>{$label|escape}</li>
		{/foreach}
	</ul>
	<div>
		{include file="prefs/shared-flags.tpl"}
		{if $p.shorthint}
			<em>{$p.shorthint|simplewiki}</em>
		{/if}
		{if $p.hint}
			<br/><em>{$p.hint|simplewiki}</em>
		{/if}
		{include file="prefs/shared-dependencies.tpl"}
	</div>
</div>
