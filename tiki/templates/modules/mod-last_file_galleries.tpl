{if $feature_file_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last modified file galleries{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastFileGalleries}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td>
<td class="module">&nbsp;<a class="linkmodule" href="tiki-list_file_gallery.php?galleryId={$modLastFileGalleries[ix].galleryId}">{$modLastFileGalleries[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}