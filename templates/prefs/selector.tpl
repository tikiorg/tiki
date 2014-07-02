<div class="form-group adminoptionbox preference clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}">
	<label class="control-label col-sm-4" for="{$p.id|escape}">{$p.name|escape}</label>
    <div class="col-sm-8">
		{object_selector _name=$p.preference _id=$p.id _value=$p.selector_value type=$p.selector_type searchable="y OR n"}
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
