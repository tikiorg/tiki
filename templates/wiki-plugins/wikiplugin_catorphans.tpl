 {* $Id$ *}

{foreach from=$orphans item=orphan}
	{if $objecttype eq 'wiki'}<a href="{$orphan.pageName|sefurl}">{$orphan.pageName|escape}</a><br>
    {elseif $objecttype eq 'file gallery'}<a href="/tiki-list_file_gallery.php?galleryId={$orphan.dataId}">Id{$orphan.dataId}: {$orphan.name|escape}</a><br>
    {elseif $objecttype eq 'tracker'}<a href="/tiki-view_tracker.php?trackerId={$orphan.dataId}">Id{$orphan.dataId}: {$orphan.name|escape}</a><br>
    {/if}
{/foreach}
{if $pagination.step ne -1}
	{pagination_links cant=$pagination.cant step=$pagination.step offset=$pagination.offset}{/pagination_links}
{/if}