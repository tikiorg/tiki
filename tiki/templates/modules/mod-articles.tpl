{if $feature_articles eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title=$title module_name="articles"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modArticles}
<tr><td   class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-read_article.php?articleId={$modArticles[ix].articleId}">{$modArticles[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}
