{if $feature_file_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Top Files{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modTopFiles}
<div class="button">
{$smarty.section.ix.index_next})
<a class="linkbut" href="tiki-download_file.php?fileId={$modTopFiles[ix].fileId}">
{$modTopFiles[ix].filename}
</a>
</div>
{/section}
</div>
</div>
{/if}