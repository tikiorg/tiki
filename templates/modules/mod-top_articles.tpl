{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_articles.tpl,v 1.4 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_articles eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Top articles{/tr}" module_name="top_articles"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopArticles}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-read_article.php?articleId={$modTopArticles[ix].articleId}">{$modTopArticles[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}