{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-old_articles.tpl,v 1.6 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_articles eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Old articles{/tr}" module_name="old_articles"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modOldArticles}
<tr><td   class="module">{$smarty.section.ix.index_next})&nbsp;<a class="linkmodule" href="tiki-read_article.php?articleId={$modOldArticles[ix].articleId}">{$modOldArticles[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}