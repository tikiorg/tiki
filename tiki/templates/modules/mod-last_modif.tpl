{if $feature_wiki eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last changes{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modLastModif}
<div class="button">{$smarty.section.ix.index_next})<a class="linkbut" href="tiki-index.php?page={$modLastModif[ix].pageName}">{$modLastModif[ix].pageName}</a></div>
{/section}
</div>
</div>
{/if}