{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_files.tpl,v 1.6 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_file_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Last Files{/tr}" module_name="last_files"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastFiles}
<tr>{if $nonums != 'y'}<td class="module">{$smarty.section.ix.index_next})</td>{/if}<td class="module">
<a class="linkmodule" href="tiki-download_file.php?fileId={$modLastFiles[ix].fileId}">
{$modLastFiles[ix].filename}
</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}