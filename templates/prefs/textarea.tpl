{strip}
<div class="adminoptionbox preference form-group clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}" style="text-align: left;">
	<label class="control-label" for="{$p.id|escape}">{$p.name|escape}</label>
	{include file="prefs/shared-flags.tpl"}
	<textarea name="{$p.preference|escape}" id="{$p.id|escape}" {if $syntax} data-syntax="{$syntax|escape}" data-codemirror="{$codemirror|escape}" {/if} class="form-control" {if $p.size} rows="{$p.size|escape}"{/if} {$p.params}>
		{$p.value|escape}
	</textarea>
	{if $p.hint}
		<div class="help-block">{$p.hint|simplewiki}</div>
	{/if}
	{include file="prefs/shared-dependencies.tpl"}
</div>
{/strip}
