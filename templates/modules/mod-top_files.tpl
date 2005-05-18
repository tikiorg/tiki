{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_files.tpl,v 1.10 2005-05-18 11:03:31 mose Exp $ *}

{if $feature_file_galleries eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` files{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top files{/tr}" assign="tpl_module_title"}
{/if}

{tikimodule title=$tpl_module_title name="top_files" flip=$module_params.flip decorations=$module_params.decorations}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopFiles}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-download_file.php?fileId={$modTopFiles[ix].fileId}">{$modTopFiles[ix].filename}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
