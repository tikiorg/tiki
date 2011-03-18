{foreach from=$field.value item=val name=ix}
	{$val|escape}{if !$smarty.foreach.ix.last}<br />{/if}
{/foreach}