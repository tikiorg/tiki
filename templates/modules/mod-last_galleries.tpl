{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last galleries{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modLastGalleries}
<div class="button">{$smarty.section.ix.index_next})<a class="linkbut" href="tiki-browse_gallery.php?galleryId={$modLastGalleries[ix].galleryId}">{$modLastGalleries[ix].name}</a></div>
{/section}
</div>
</div>
{/if}