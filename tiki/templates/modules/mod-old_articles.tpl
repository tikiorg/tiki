{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Old articles{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modOldArticles}
<div class="button">{$smarty.section.ix.index_next})&nbsp;<a class="linkbut" href="tiki-read_article.php?articleId={$modOldArticles[ix].articleId}">{$modOldArticles[ix].title}</a></div>
{/section}
</div>
</div>
{/if}