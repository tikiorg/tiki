{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-forums_last_topics.tpl,v 1.3 2004-01-16 18:02:00 musus Exp $ *}

{if $feature_forums eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` forum topics{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last forum topics{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="forums_last_topics"}
  <table>
    {section name=ix loop=$modForumsLastTopics}
      <tr class="module">
        {if $nonums != 'y'}<td valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td>
          <a class="linkmodule" href="{$modForumsLastTopics[ix].href}">
            {$modForumsLastTopics[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
