{if $feature_wiki eq 'y'}
<div class="box">
<div class="box-title">
{tr}Top Pages{/tr}
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