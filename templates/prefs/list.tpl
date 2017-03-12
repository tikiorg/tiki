{* $Id$ *}
<div class="adminoptionbox preference form-group clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}" style="text-align: left;">
	<label class="col-sm-4 control-label" for="{$p.id|escape}">{$p.name|escape|breakline}</label>
	<div class="col-sm-8">
		<select class="form-control" name="{$p.preference|escape}" id="{$p.id|escape}" data-tiki-admin-child-block=".{$p.preference|escape}_childcontainer">
			{foreach from=$p.options key=value item=label}
				<option value="{$value|escape}"{if $value eq $p.value} selected="selected"{/if} {$p.params}>{$label|escape}</option>
			{/foreach}
		</select>
		{include file="prefs/shared-flags.tpl"}
		{if $p.shorthint}
			<div class="help-block">{$p.shorthint|simplewiki}</div>
		{/if}
		{if $p.detail}
			<div class="help-block">{$p.detail|simplewiki}</div>
		{/if}
		{if $p.hint}
			<div class="help-block">{$p.hint|simplewiki}</div>
		{/if}
		{include file="prefs/shared-dependencies.tpl"}
	</div>
</div>
