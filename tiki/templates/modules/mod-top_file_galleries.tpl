{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_file_galleries.tpl,v 1.9 2005-03-12 16:51:00 mose Exp $ *}

{if $feature_file_galleries eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` File Galleries{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top File Galleries{/tr}" assign="tpl_module_title"}
{/if}

{tikimodule title=$tpl_module_title name="top_file_galleries" flip=$module_params.flip decorations=$module_params.decorations}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopFileGalleries}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-list_file_gallery.php?galleryId={$modTopFileGalleries[ix].galleryId}">{$modTopFileGalleries[ix].name}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
