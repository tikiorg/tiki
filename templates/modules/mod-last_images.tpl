{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_images.tpl,v 1.7 2007-10-14 17:51:01 mose Exp $ *}

{if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Images{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Images{/tr}" assign="tpl_module_title"}
{/if}
{/if}

{tikimodule title=$tpl_module_title name="last_images" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}

<table border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastImages}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-browse_image.php?imageId={$modLastImages[ix].imageId}">{$modLastImages[ix].name}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
