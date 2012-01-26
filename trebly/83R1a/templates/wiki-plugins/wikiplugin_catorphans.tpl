 {* $Id: wikiplugin_catorphans.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{foreach from=$pages item=pg}
		 <a href="{$pg.pageName|sefurl}">{$pg.pageName|escape}</a><br />
{/foreach}
{if $pagination.step ne -1}
	{pagination_links cant=$pagination.cant step=$pagination.step offset=$pagination.offset}{/pagination_links}
{/if}