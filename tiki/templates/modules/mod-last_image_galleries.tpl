{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_image_galleries.tpl,v 1.6 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Last galleries{/tr}" module_name="last_image_galleries"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastGalleries}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modLastGalleries[ix].galleryId}">{$modLastGalleries[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}