{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-article_topics.tpl,v 1.1 2005-12-13 13:57:42 sylvieg Exp $ *}

{if $feature_articles eq 'y'}
{tikimodule title=$module_title name="articles" flip=$module_params.flip decorations=$module_params.decorations}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$listTopics}
    {if $listTopics[ix].arts > 0}
    <tr>
      {if $nonums != 'y'}
        <td class="module" valign="top">{$smarty.section.ix.index_next})</td>
      {/if}
      <td class="module">&nbsp;
        <a class="linkmodule" href="tiki-view_articles.php?topic={$listTopics[ix].topicId}">
          {$listTopics[ix].name}
        </a>
        </td>
    </tr>
    {/if}
    {/section}
  </table>
{/tikimodule}
{/if}
