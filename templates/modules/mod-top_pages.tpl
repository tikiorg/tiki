{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_pages.tpl,v 1.7 2003-10-20 01:13:16 zaufi Exp $ *}

{if $feature_wiki eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Top Pages{/tr}" module_name="top_pages"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopPages}
<tr>{if $nonums != 'y'}<td class="module" valign='top'>{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-index.php?page={$modTopPages[ix].pageName}">{$modTopPages[ix].pageName}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}