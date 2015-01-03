<div class="adminoptionbox preference clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}" style="text-align: left;">
	<label class="col-md-4 control-label" for="{$p.id|escape}">{$p.name|escape}</label>
	<div class="col-md-8">
		<select class="form-control" name="{$p.preference|escape}[]" id="{$p.id|escape}" multiple="multiple">
			{foreach from=$p.options key=value item=label}
				<option value="{$value|escape}"{if in_array($value, $p.value)} selected="selected"{/if} {$p.params}>{$label|escape}</option>
			{/foreach}
		</select>
		{include file="prefs/shared-flags.tpl"}
		{if $p.shorthint}
			<div class="help-block">{$p.shorthint|simplewiki}</div>
		{/if}
		{if $p.hint}
			<div class="help-block">{$p.hint|simplewiki}</div>
		{/if}
		{include file="prefs/shared-dependencies.tpl"}
		{if $prefs.jquery_ui_chosen neq 'y'}
			{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
		{/if}
	</div>
</div>
