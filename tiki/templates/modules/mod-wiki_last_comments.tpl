{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-wiki_last_comments.tpl,v 1.2 2003-11-24 20:52:07 zaufi Exp $ *}

{if $feature_forums eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` wiki comments{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last wiki comments{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="wiki_last_comments"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$comments}
      <tr>
        {if $nonums != 'y'}<td valign="top" class="module">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-index.php?page={$comments[ix].page|escape}" title="{$comments[ix].commentDate|tiki_short_datetime}, by {$comments[ix].user}{if $moretooltips eq 'y'} on page {$comments[ix].page}{/if}">
            {if $moretooltips ne 'y'}<b>{$comments[ix].page}:</b>{/if} {$comments[ix].title}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
