<div class="adminoptionbox preference clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}">
	{if $p.name}
		<label for="{$p.id|escape}" class="control-label col-md-4">{$p.name|escape}</label>
	{/if}
	<div class="col-md-8">
		{foreach from=$p.options key=value item=label name=loop}
			<div class="adminoptionlabel">
				<input id="{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}" type="radio" name="{$p.preference|escape}"
					value="{$value}"{if $p.value eq $value} checked="checked"{/if} {$p.params}
					data-tiki-admin-child-block="#{$p.preference|escape}_childcontainer_{$smarty.foreach.loop.index|escape}"
				>
				<label for="{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}">{$label|escape}</label>
			</div>
		{/foreach}
		{if $p.detail}
			{$p.detail|simplewiki}
		{/if}
		{include file="prefs/shared-flags.tpl"}
		{if $p.hint}
			<div class="help-block">{$p.hint|simplewiki}</div>
		{/if}
		{include file="prefs/shared-dependencies.tpl"}
	</div>
</div>
