{if $feature_articles eq 'y'}
<div class="box">
<div class="box-title">
{tr}Top articles{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modTopArticles}
<div class="button">{$smarty.section.ix.index_next})&nbsp;<a class="linkbut" href="tiki-read_article.php?articleId={$modTopArticles[ix].articleId}">{$modTopArticles[ix].title}</a></div>
{/section}
</div>
</div>
{/if}