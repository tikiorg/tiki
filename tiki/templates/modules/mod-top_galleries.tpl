<div class="box">
<div class="box-title">
{tr}Top galleries{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modTopGalleries}
<div class="button">{$smarty.section.ix.index_next})<a class="linkbut" href="tiki-browse_gallery.php?galleryId={$modTopGalleries[ix].galleryId}">{$modTopGalleries[ix].name}</a></div>
{/section}
</div>
</div>