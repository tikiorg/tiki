{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_image_galleries.tpl,v 1.6 2003-11-20 23:49:04 mose Exp $ *}

{if $user}
{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}My galleries{/tr}" module_name="user_image_galleries"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modUserG}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modUserG[ix].galleryId}">{$modUserG[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}
{/if}