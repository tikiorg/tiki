{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_articles.tpl,v 1.2 2003-10-20 01:13:16 zaufi Exp $ *}
{if $feature_articles eq 'y'}
<div class="box">
<div class="box-title">
<a href="tiki-view_articles.php">{tr}Last articles{/tr}</a>
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="2">
{section name=ix loop=$modLastArticles}
<tr>{if $nonums != 'y'}<td class="module">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-read_article.php?articleId={$modLastArticles[ix].articleId}">
{$modLastArticles[ix].title}<br />{$modLastArticles[ix].publishDate|tiki_short_datetime}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}
