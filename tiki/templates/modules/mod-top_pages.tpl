{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_pages.tpl,v 1.5 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_wiki eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Top Pages{/tr}" module_name="top_pages"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopPages}
<tr><td  width="5%" class="module" valign='top'>{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-index.php?page={$modTopPages[ix].pageName}">{$modTopPages[ix].pageName}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}