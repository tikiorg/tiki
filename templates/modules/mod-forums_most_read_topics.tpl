{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_most_read_topics.tpl,v 1.6 2003-11-23 03:15:07 zaufi Exp $ *}

{if $feature_forums eq 'y'}
{tikimodule title="{tr}Most read topics{/tr}" name="forums_most_read_topics"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modForumsMostReadTopics}
      <tr>
        {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="{$modForumsMostReadTopics[ix].href}">
            {$modForumsMostReadTopics[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
