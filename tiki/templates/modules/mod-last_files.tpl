{if $feature_file_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last Files{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastFiles}
<tr><td class="module">{$smarty.section.ix.index_next})</td><td class="module">
<a class="linkmodule" href="tiki-download_file.php?fileId={$modLastFiles[ix].fileId}">
{$modLastFiles[ix].filename}
</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}