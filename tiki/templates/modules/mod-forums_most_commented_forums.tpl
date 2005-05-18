{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_most_commented_forums.tpl,v 1.9 2005-05-18 11:03:29 mose Exp $ *}

{if $feature_forums eq 'y'}
{if $nonums eq 'y'}
{eval var="`$module_rows` {tr}Most commented forums{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Most commented forums{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="forums_most_commented_forums" flip=$module_params.flip decorations=$module_params.decorations}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modForumsMostCommentedForums}
      <tr>
        {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="{$modForumsMostCommentedForums[ix].href}">
            {$modForumsMostCommentedForums[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
