{if $feature_wiki}
<div class="box">
<div class="box-title">
{tr}My Pages{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modUserPages}
<div class="button">{$smarty.section.ix.index_next})<a class="linkbut" href="tiki-index.php?page={$modUserPages[ix].pageName}">{$modUserPages[ix].pageName}</a></div>
{/section}
</div>
</div>
{/if}