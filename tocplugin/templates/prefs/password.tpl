<div class="adminoptionbox preference clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}">
	<label class="col-sm-4 control-label" for="{$p.id|escape}">{$p.name|escape}</label>
	<div class="col-sm-8">
		<input name="{$p.preference|escape}" id="{$p.id|escape}" value="{$p.value|escape}" class="form-control" {* size="{$p.size|default:80|escape}" *} type="password" {$p.params}>
		{$p.detail|escape}
		{include file="prefs/shared-flags.tpl"}
		{if $p.shorthint}
			<div class="help-block">{$p.shorthint|simplewiki}</div>
		{/if}
		{if $p.hint}
			<div class="help-block">{$p.hint|simplewiki}</div>
		{/if}
		{include file="prefs/shared-dependencies.tpl"}
	</div>
</div>
