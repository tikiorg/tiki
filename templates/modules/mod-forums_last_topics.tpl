{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_last_topics.tpl,v 1.6 2003-11-23 03:15:07 zaufi Exp $ *}

{if $feature_forums eq 'y'}
{tikimodule title="{tr}Last forum topics{/tr}" name="forums_last_topics"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modForumsLastTopics}
      <tr>
        {if $nonums != 'y'}<td valign="top" class="module">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="{$modForumsLastTopics[ix].href}">
            {$modForumsLastTopics[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
