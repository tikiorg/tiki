{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_file_galleries.tpl,v 1.4 2003-09-25 01:05:22 rlpowell Exp $ *}

{if $feature_file_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last modified file galleries{/tr}" module_name="last_file_galleries"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastFileGalleries}
<tr><td   class="module" valign="top">{$smarty.section.ix.index_next})</td>
<td class="module">&nbsp;<a class="linkmodule" href="tiki-list_file_gallery.php?galleryId={$modLastFileGalleries[ix].galleryId}">{$modLastFileGalleries[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}