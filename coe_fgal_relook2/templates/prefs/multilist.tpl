<div class="adminoptionbox preference clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}" style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
	<select name="{$p.preference|escape}[]" id="{$p.id|escape}" multiple="multiple">
		{foreach from=$p.options key=value item=label}
			<option value="{$value|escape}"{if in_array($value, $p.value)} selected="selected"{/if} {$p.params}>{$label|escape}</option>
		{/foreach}
	</select>
	{include file="prefs/shared-flags.tpl"}
	{if $p.shorthint}
		<em>{$p.shorthint|simplewiki}</em>
	{/if}
	{if $p.hint}
		<br/><em>{$p.hint|simplewiki}</em>
	{/if}
	{include file="prefs/shared-dependencies.tpl"}
	{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
</div>
