{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_images.tpl,v 1.7 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Top Images{/tr}" module_name="top_images"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopImages}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">{$modTopImages[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}