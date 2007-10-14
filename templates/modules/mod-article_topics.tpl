{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-article_topics.tpl,v 1.4 2007-10-14 17:51:00 mose Exp $ *}

{if $prefs.feature_articles eq 'y'}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}$module_title{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="article_topics" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
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
