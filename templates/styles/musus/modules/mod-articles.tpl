{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-articles.tpl,v 1.2 2004-01-16 17:59:36 musus Exp $ *}

{if $feature_articles eq 'y'}
{tikimodule title=$title name="articles"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modArticles}
    <tr class="module">
      {if $nonums != 'y'}
        <td valign="top">{$smarty.section.ix.index_next})</td>
      {/if}
      <td>&nbsp;
        <a class="linkmodule" href="tiki-read_article.php?articleId={$modArticles[ix].articleId}">
          {$modArticles[ix].title}
        </a>
        </td>
    </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
