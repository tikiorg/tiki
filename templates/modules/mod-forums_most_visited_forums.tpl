{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_most_visited_forums.tpl,v 1.6 2003-11-23 03:15:07 zaufi Exp $ *}

{if $feature_forums eq 'y'}
{tikimodule title="{tr}Most visited forums{/tr}" name="forums_most_visited_forums"}

  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modForumsMostVisitedForums}
    <tr>
      {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
      <td class="module">
        <a class="linkmodule" href="{$modForumsMostVisitedForums[ix].href}">
          {$modForumsMostVisitedForums[ix].name}
        </a>
      </td>
    </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
