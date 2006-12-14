{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_articles.tpl,v 1.13 2006-12-14 16:40:33 sylvieg Exp $ *}

{if $feature_articles eq 'y'}
{if $nonums eq 'y'}
{eval var="<a href=\"tiki-view_articles.php?topic=$topicId&type=$type\">{tr}Last `$module_rows` articles{/tr}</a>" assign="tpl_module_title"}
{else}
{eval var="<a href=\"tiki-view_articles.php?topic=$topicId&type=$type\">{tr}Last articles{/tr}</a>" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_articles" flip=$module_params.flip decorations=$module_params.decorations}
  <table  border="0" cellpadding="1" cellspacing="0" width="100%">
    {section name=ix loop=$modLastArticles}
      <tr>
        {if $nonums != 'y'}<td class="module">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
		  {if $absurl == 'y'}
          <a class="linkmodule" href="{$feature_server_name}tiki-read_article.php?articleId={$modLastArticles[ix].articleId}" title="{$modLastArticles[ix].publishDate|tiki_short_datetime}, {tr}by{/tr} {$modLastArticles[ix].author}">
            {$modLastArticles[ix].title}
          </a>
		  {else}
		  <a class="linkmodule" href="tiki-read_article.php?articleId={$modLastArticles[ix].articleId}" title="{$modLastArticles[ix].publishDate|tiki_short_datetime}, {tr}by{/tr} {$modLastArticles[ix].author}">
            {$modLastArticles[ix].title}
          </a>
		  {/if}
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
