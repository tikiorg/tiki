{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_best_voted_topics.tpl,v 1.6 2003-11-23 03:15:07 zaufi Exp $ *}

{if $feature_forums eq 'y'}
{tikimodule title="{tr}Top topics{/tr}" name="forums_best_voted_topics"}
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
