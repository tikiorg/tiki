{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_image_galleries.tpl,v 1.8 2003-11-24 01:37:55 gmuslera Exp $ *}

{if $feature_galleries eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` galleries{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top galleries{/tr}" assign="tpl_module_title"}
{/if}

{tikimodule title="{tr}Top galleries{/tr}" name="top_image_galleries"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopGalleries}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modTopGalleries[ix].galleryId}">{$modTopGalleries[ix].name}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
