<input class="system" type="hidden" name="lm_preference[]" value="{$p.preference|escape}">
{if $p.dependencies}
	{foreach from=$p.dependencies item=dep}
		{if $dep.met}
			{icon name="ok" class="pref_dependency tips text-success" title="{tr}Requires:{/tr} "|cat:$dep.label|escape|cat:" (OK)"}
		{elseif $dep.type eq 'profile'}
			<div class="alert alert-warning pref_dependency highlight"{if not $p.modified} style="display:none;"{/if}>{tr}You need apply profile{/tr} <a href="{$dep.link|escape}" class="alert-link">{$dep.label|escape}</a></div>
		{else}
			<div class="alert alert-warning pref_dependency highlight"{if not $p.modified} style="display:none;"{/if}>{tr}You need to set{/tr} <a href="{$dep.link|escape}" class="alert-link">{$dep.label|escape}</a></div>
		{/if}
	{/foreach}
{/if}
{if $p.packages_required}
	{foreach from=$p.packages_required item=dep}
		{if $dep.met}
			{icon name="ok" class="pref_dependency tips text-success" title="{tr}Requires package:{/tr} "|cat:$dep.label|escape|cat:" (OK)"}
		{else}
			<div class="alert alert-warning pref_dependency highlight"{if not $p.modified and not $p.value} style="display:none;"{/if}>{tr}Missing package:{/tr} <a href="{$dep.link|escape}" class="alert-link">{$dep.label|escape}</a></div>
		{/if}
	{/foreach}
{/if}
{foreach from=$p.notes item=note}
	<div class="help-block pref_note">{$note|escape}</div>
{/foreach}
