<input class="system" type="hidden" name="lm_preference[]" value="{$p.preference|escape}" />
{if $p.dependencies}
	{foreach from=$p.dependencies item=dep}
		{if $dep.met}
			<div class="pref_dependency">{tr}Requires{/tr} <a href="{$dep.link|escape}">{$dep.label|escape}</a> (OK)</div>
		{else}
			<div class="pref_dependency highlight">{tr}You need to set{/tr} <a href="{$dep.link|escape}">{$dep.label|escape}</a></div>
		{/if}
	{/foreach}
{/if}
