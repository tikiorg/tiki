{if $feature_file_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last modified file galleries{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modLastFileGalleries}
<div class="button">{$smarty.section.ix.index_next})
<a class="linkbut" href="tiki-list_file_gallery.php?galleryId={$modLastFileGalleries[ix].galleryId}">{$modLastFileGalleries[ix].name}</a></div>
{/section}
</div>
</div>
{/if}