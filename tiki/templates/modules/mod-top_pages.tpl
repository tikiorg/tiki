{if $feature_wiki eq 'y'}
<div class="box">
<div class="box-title">
{tr}Top Pages{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modTopPages}
<div class="button">{$smarty.section.ix.index_next})<a class="linkbut" href="tiki-index.php?page={$modTopPages[ix].pageName}">{$modTopPages[ix].pageName}</a></div>
{/section}
</div>
</div>
{/if}