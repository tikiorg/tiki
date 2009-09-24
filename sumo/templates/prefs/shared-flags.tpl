{if $p.helpurl}
	<a href="{$p.helpurl|escape}" target="tikihelp" class="tikihelp" title="{$p.name|escape}: {$p.description|escape}">
		{icon _id=help alt=$p.name}
	</a>
{/if}
{if $p.warning}
	<a href="" target="tikihelp" class="tikihelp" title="{tr}Warning{/tr}: {$p.warning|escape}">
		{icon _id=error alt={tr}Warning{/tr}}
	</a>
{/if}
