{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_articles.tpl,v 1.6 2003-11-23 03:15:07 zaufi Exp $ *}

{if $feature_articles eq 'y'}
{tikimodule title="<a href=\"tiki-view_articles.php\">{tr}Last articles{/tr}</a>" name="last_articles"}
  <table width="100%" border="0" cellpadding="0" cellspacing="2">
    {section name=ix loop=$modLastArticles}
      <tr>
        {if $nonums != 'y'}<td class="module">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-read_article.php?articleId={$modLastArticles[ix].articleId}">
            {$modLastArticles[ix].title}<br />{$modLastArticles[ix].publishDate|tiki_short_datetime}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
