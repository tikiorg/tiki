{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_files.tpl,v 1.3 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_file_galleries eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Top Files{/tr}" module_name="top_files"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopFiles}
<tr><td width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td>
<td class="module">&nbsp;<a class="linkmodule" href="tiki-download_file.php?fileId={$modTopFiles[ix].fileId}">{$modTopFiles[ix].filename}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}