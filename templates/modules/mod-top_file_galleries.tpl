{if $feature_file_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Top File Galleries{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modTopFileGalleries}
<div class="button">{$smarty.section.ix.index_next})
<a class="linkbut" href="tiki-list_file_gallery.php?galleryId={$modTopFileGalleries[ix].galleryId}">{$modTopFileGalleries[ix].name}</a></div>
{/section}
</div>
</div>
{/if}