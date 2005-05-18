{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_images.tpl,v 1.11 2005-05-18 11:03:31 mose Exp $ *}

{if $feature_galleries eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` Images{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Images{/tr}" assign="tpl_module_title"}
{/if}

{tikimodule title=$tpl_module_title name="top_images" flip=$module_params.flip decorations=$module_params.decorations}

<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopImages}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">{$modTopImages[ix].name}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
