{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_images.tpl,v 1.4 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Top Images{/tr}" module_name="top_images"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopImages}
<tr><td width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">{$modTopImages[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}