<div style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
	<input name="{$p.preference|escape}" id="{$p.id|escape}" value="{$p.value|escape}" size="{$p.size|default:80|escape}" type="text">
	{if $p.helpurl}
		<a href="{$p.helpurl|escape}" target="tikihelp" class="tikihelp" title="{$p.name|escape}: {$p.description|escape}">
			{icon _id=help alt=$p.name}
		</a>
	{/if}
	<input type="hidden" name="lm_preference[]" value="{$p.preference|escape}"/>
</div>
