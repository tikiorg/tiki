{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_best_voted_topics.tpl,v 1.8 2003-12-08 15:04:29 sylvieg Exp $ *}

{if $feature_forums eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` topics{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top topics{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="forums_best_voted_topics"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modForumsTopTopics}
      <tr>
        {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="{$modForumsTopTopics[ix].href}">
            {$modForumsTopTopics[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
