{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_images.tpl,v 1.8 2003-11-23 04:01:52 gmuslera Exp $ *}

{if $feature_galleries eq 'y'}
{tikimodule title="{tr}Top Images{/tr}" name="top_images"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopImages}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">{$modTopImages[ix].name}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}