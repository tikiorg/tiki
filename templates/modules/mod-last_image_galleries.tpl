{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_image_galleries.tpl,v 1.3 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last galleries{/tr}" module_name="last_image_galleries"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastGalleries}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modLastGalleries[ix].galleryId}">{$modLastGalleries[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}