<div class="half_width">
	<span class="checkbox"><input id="{$p.id|escape}" type="checkbox" name="{$p.preference|escape}" {if $p.value eq 'y'}checked="checked"{/if}/></span>
	<span class="label" >
		<label for="{$p.id|escape}">{$p.name|escape}</label>
		{include file=prefs/shared-flags.tpl}
	</span>
	{include file=prefs/shared-dependencies.tpl}
</div>
