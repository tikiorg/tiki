{* $Id$ *}
<div class="adminoptionbox preference form-group clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}" style="text-align: left;">
	<label class="col-md-4 control-label" for="{$p.id|escape}">{$p.name|escape|breakline}</label>
	<div class="col-md-8">
		<select class="form-control" name="{$p.preference|escape}" id="{$p.id|escape}" data-tiki-admin-child-block=".{$p.preference|escape}_childcontainer">
			{foreach from=$p.options key=value item=label}
				<option value="{$value|escape}"{if $value eq $p.value} selected="selected"{/if} {$p.params}>{$label|escape}</option>
			{/foreach}
		</select>
		{include file="prefs/shared-flags.tpl"}
		{if $p.shorthint}
			<em>{$p.shorthint|simplewiki}</em>
		{/if}
		{if $p.detail}
			<br/>{$p.detail|simplewiki}
		{/if}
		{if $p.hint}
			<br/><em>{$p.hint|simplewiki}</em>
		{/if}
		{include file="prefs/shared-dependencies.tpl"}
	</div>
</div>
