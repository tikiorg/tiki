<div style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
	<select name="{$p.preference|escape}" id="{$p.id|escape}">
		{foreach from=$p.options key=value item=label}
			<option value="{$value|escape}"{if $value eq $p.value} selected="selected"{/if}>{$label|escape}</option>
		{/foreach}
	</select>
	{if $p.helpurl}
		<a href="{$p.helpurl|escape}" target="tikihelp" class="tikihelp" title="{$p.name|escape}: {$p.description|escape}">
			{icon _id=help alt=$p.name}
		</a>
	{/if}
	<input type="hidden" name="lm_preference[]" value="{$p.preference|escape}"/>
</div>
