{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_image_galleries.tpl,v 1.4 2003-09-25 01:05:23 rlpowell Exp $ *}

{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Top galleries{/tr}" module_name="top_image_galleries"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopGalleries}
<tr><td   class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modTopGalleries[ix].galleryId}">{$modTopGalleries[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}