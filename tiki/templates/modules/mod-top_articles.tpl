{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_articles.tpl,v 1.11 2005-05-18 11:03:31 mose Exp $ *}

{if $feature_articles eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` articles{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top articles{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="top_articles" flip=$module_params.flip decorations=$module_params.decorations}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopArticles}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-read_article.php?articleId={$modTopArticles[ix].articleId}">{$modTopArticles[ix].title}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
