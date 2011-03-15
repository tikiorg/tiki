{foreach from=$pages item=pg}
		 <a href="{$pg.pageName|sefurl}">{$pg.pageName|escape}</a><br />
{/foreach}
{if $pagination.step ne -1}
	{pagination_links cant=$pagination.cant step=$pagination.step offset=$pagination.offset}{/pagination_links}
{/if}
