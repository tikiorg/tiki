{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_articles.tpl,v 1.3 2003-11-13 10:21:05 ohertel Exp $ *}
{if $feature_articles eq 'y'}
<div class="box">
<div class="box-title">
    {include file="modules/module-title.tpl" module_title="<a class=\"box-title\" href=\"tiki-view_articles.php\">{tr}Last articles{/tr}</a>" module_name="last_articles"}
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
