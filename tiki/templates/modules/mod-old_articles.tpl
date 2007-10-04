{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-old_articles.tpl,v 1.11 2007-10-04 22:17:47 nyloth Exp $ *}

{if $prefs.feature_articles eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Old articles{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="old_articles" flip=$module_params.flip decorations=$module_params.decorations}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modOldArticles}
<tr><td   class="module">{$smarty.section.ix.index_next})&nbsp;<a class="linkmodule" href="tiki-read_article.php?articleId={$modOldArticles[ix].articleId}">{$modOldArticles[ix].title}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}
