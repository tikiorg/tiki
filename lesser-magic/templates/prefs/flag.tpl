<div class="half_width">
	<span class="checkbox"><input id="{$p.id|escape}" type="checkbox" name="{$p.preference|escape}" {if $p.value eq 'y'}checked="checked"{/if}/></span>
	<span class="label" >
		<label for="{$p.id|escape}">{$p.name|escape}</label>
		{if $p.helpurl}
			<a href="{$p.helpurl|escape}" target="tikihelp" class="tikihelp" title="{$p.name|escape}: {$p.description|escape}">
				{icon _id=help alt=$p.name}
			</a>
		{/if}
	</span>
	{include file=prefs/shared-dependencies.tpl}
</div>
