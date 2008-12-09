{* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/files/templates/wikiplugin_files.tpl,v 1.1 2008/01/18 22:00:48 sylvieg Exp $ *}

{if $data}<h4>{$data|escape}</h4>{else}
<h4>
{if $category}
	{if is_array($category)}
		{tr}Categories:{/tr}
		{section name=ix loop=$category}
			{$category[ix]|escape}
			{if !$smarty.section.ix.last}, {/if}
		{/section}
	{else}
		{tr}Category:{/tr} {$category|escape}
	{/if}
	<br />
{/if}
{if $gal_info.name}{tr}File Gallery:{/tr} <a href="tiki-list_file_gallery.php?galleryId={$gal_info.galleryId}" title="{tr}list{/tr}">{$gal_info.name|escape}</a>{/if}
</h4>
{/if}
{if $show_find eq 'y'}
	{include file="find.tpl"}
{/if}
{include file="list_file_gallery_content.tpl"}