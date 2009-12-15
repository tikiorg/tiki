<input type="hidden" name="lm_preference[]" value="{$p.preference|escape}" />
{if $p.dependencies}
	{foreach from=$p.dependencies item=dep}
		<br/>
		{if $dep.met}
			<span>{tr}Requires{/tr} <a href="{$dep.link|escape}">{$dep.label|escape}</a> (OK)</span>
		{else}
			<span class="highlight">{tr}You need to set{/tr} <a href="{$dep.link|escape}">{$dep.label|escape}</a></span>
		{/if}
	{/foreach}
{/if}
