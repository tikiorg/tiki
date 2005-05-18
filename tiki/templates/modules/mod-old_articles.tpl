{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-old_articles.tpl,v 1.9 2005-05-18 11:03:30 mose Exp $ *}

{if $feature_articles eq 'y'}
{tikimodule title="{tr}Old articles{/tr}" name="old_articles" flip=$module_params.flip decorations=$module_params.decorations}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modOldArticles}
<tr><td   class="module">{$smarty.section.ix.index_next})&nbsp;<a class="linkmodule" href="tiki-read_article.php?articleId={$modOldArticles[ix].articleId}">{$modOldArticles[ix].title}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
