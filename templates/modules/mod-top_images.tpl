{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Top Images{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modTopImages}
<div class="button">
{$smarty.section.ix.index_next})
<a class="linkbut" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">
{$modTopImages[ix].name}
</a>
</div>
{/section}
</div>
</div>
{/if}