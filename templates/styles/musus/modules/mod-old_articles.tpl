{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-old_articles.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $feature_articles eq 'y'}
{tikimodule title="{tr}Old articles{/tr}" name="old_articles"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modOldArticles}
<tr><td   class="module">{$smarty.section.ix.index_next})&nbsp;<a class="linkmodule" href="tiki-read_article.php?articleId={$modOldArticles[ix].articleId}">{$modOldArticles[ix].title}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}