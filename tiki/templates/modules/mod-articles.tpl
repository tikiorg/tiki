{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-articles.tpl,v 1.3 2003-11-23 03:15:06 zaufi Exp $ *}

{if $feature_articles eq 'y'}
{tikimodule title=$title name="articles"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modArticles}
    <tr>
      {if $nonums != 'y'}
        <td class="module" valign="top">{$smarty.section.ix.index_next})</td>
      {/if}
      <td class="module">&nbsp;
        <a class="linkmodule" href="tiki-read_article.php?articleId={$modArticles[ix].articleId}">
          {$modArticles[ix].title}
        </a>
        </td>
    </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
