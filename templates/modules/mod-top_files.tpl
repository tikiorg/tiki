{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_files.tpl,v 1.7 2003-11-23 04:01:52 gmuslera Exp $ *}

{if $feature_file_galleries eq 'y'}
{tikimodule title="{tr}Top Files{/tr}" name="top_files"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopFiles}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-download_file.php?fileId={$modTopFiles[ix].fileId}">{$modTopFiles[ix].filename}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}