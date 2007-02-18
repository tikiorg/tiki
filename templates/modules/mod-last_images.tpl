{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_images.tpl,v 1.5 2007-02-18 11:21:16 mose Exp $ *}

{if $feature_galleries eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Images{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Images{/tr}" assign="tpl_module_title"}
{/if}
{/if}

{tikimodule title=$tpl_module_title name="last_images" flip=$module_params.flip decorations=$module_params.decorations}

<table border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastImages}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-browse_image.php?imageId={$modLastImages[ix].imageId}">{$modLastImages[ix].name}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
