{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-old_articles.tpl,v 1.7 2003-11-23 03:53:04 zaufi Exp $ *}

{if $feature_articles eq 'y'}
{tikimodule title="{tr}Old articles{/tr}" name="old_articles"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modOldArticles}
<tr><td   class="module">{$smarty.section.ix.index_next})&nbsp;<a class="linkmodule" href="tiki-read_article.php?articleId={$modOldArticles[ix].articleId}">{$modOldArticles[ix].title}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}