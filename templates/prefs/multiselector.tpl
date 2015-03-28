<div class="form-group adminoptionbox preference clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}">
	<label class="control-label col-sm-4" for="{$p.id|escape}">{$p.name|escape}</label>
	<div class="col-sm-8">
		{object_selector_multi _simplename=$p.preference _simpleid=$p.id _simplevalue=$p.value _separator=$p.separator type=$p.selector_type _format=$p.format|default:null}
		{if $p.shorthint}
			<div class="help-block">{$p.shorthint|simplewiki}</div>
		{/if}

		{include file="prefs/shared-flags.tpl"}

		{if $p.detail}
			<div class="help-block">{$p.detail|simplewiki}</div>
		{/if}

		{if $p.hint}
			<div class="help-block">{$p.hint|simplewiki}</div>
		{/if}

		{include file="prefs/shared-dependencies.tpl"}
	</div>
</div>
