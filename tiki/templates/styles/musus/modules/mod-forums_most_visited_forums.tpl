{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-forums_most_visited_forums.tpl,v 1.2 2004-01-16 18:03:16 musus Exp $ *}

{if $feature_forums eq 'y'}
{if $nonums eq 'y'}
{eval var="`$module_rows` {tr}Most visited forums{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Most visited forums{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="forums_most_visited_forums"}
  <table border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modForumsMostVisitedForums}
    <tr class="module">
      {if $nonums != 'y'}<td valign="top">{$smarty.section.ix.index_next})</td>{/if}
      <td>
        <a class="linkmodule" href="{$modForumsMostVisitedForums[ix].href}">
          {$modForumsMostVisitedForums[ix].name}
        </a>
      </td>
    </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
