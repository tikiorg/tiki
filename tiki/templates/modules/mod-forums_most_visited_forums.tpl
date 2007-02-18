{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_most_visited_forums.tpl,v 1.10 2007-02-18 11:21:16 mose Exp $ *}

{if $feature_forums eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="`$module_rows` {tr}Most visited forums{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Most visited forums{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="forums_most_visited_forums" flip=$module_params.flip decorations=$module_params.decorations}
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
