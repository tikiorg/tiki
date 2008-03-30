{* $Id$ *}

{if $prefs.feature_forums eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` forum topics{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last forum topics{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="forums_last_topics" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <table  border="0" cellpadding="1" cellspacing="0" width="100%">
    {section name=ix loop=$modForumsLastTopics}
      <tr>
        {if $nonums != 'y'}<td valign="top" class="module">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
		  {if $absurl == 'y'}
          <a class="linkmodule" href="{$base_url}{$modForumsLastTopics[ix].href}" title="{$modForumsLastTopics[ix].date|tiki_short_datetime}, {tr}by{/tr} {if $modForumsLastTopics[ix].user ne ''}{$modForumsLastTopics[ix].user}{else}{tr}Anonymous{/tr}{/if}">
            {$modForumsLastTopics[ix].name}
          </a>
		  {else}
          <a class="linkmodule" href="{$modForumsLastTopics[ix].href}" title="{$modForumsLastTopics[ix].date|tiki_short_datetime}, {tr}by{/tr} {if $modForumsLastTopics[ix].user ne ''}{$modForumsLastTopics[ix].user}{else}{tr}Anonymous{/tr}{/if}">
            {$modForumsLastTopics[ix].name}
          </a>
		  {/if}
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
