{if $feature_articles eq 'y'}
<div class="box">
<div class="box-title">
{tr}Old articles{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modOldArticles}
<tr><td  width="5%" class="module">{$smarty.section.ix.index_next})&nbsp;<a class="linkmodule" href="tiki-read_article.php?articleId={$modOldArticles[ix].articleId}">{$modOldArticles[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}