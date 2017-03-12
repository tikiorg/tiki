<div class="adminoptionbox preference clearfix multicheckbox form-group {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}" style="text-align: left;">
	<label for="{$p.id|escape}" class="control-label col-sm-4">{$p.name|escape}</label>
	<div class="col-sm-8">
			{foreach from=$p.options key=value item=label}
				<label class="control-label"><input style="margin-left:5px" type="checkbox" name="{$p.preference|escape}[]" value="{$value|escape}"{if in_array($value, $p.value)} checked="checked"{/if} {$p.params}>
						{$label|escape}
				</label>
			{/foreach}
		<div>
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
</div>
