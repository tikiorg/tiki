{if $feature_file_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last Files{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modLastFiles}
<div class="button">
{$smarty.section.ix.index_next})
<a class="linkbut" href="tiki-download_file.php?fileId={$modLastFiles[ix].fileId}">
{$modLastFiles[ix].filename}
</a>
</div>
{/section}
</div>
</div>
{/if}