{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_image_galleries.tpl,v 1.3 2003-08-07 20:56:53 zaufi Exp $ *}

{if $user}
{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}My galleries{/tr}" module_name="user_image_galleries"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modUserG}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modUserG[ix].galleryId}">{$modUserG[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}
{/if}