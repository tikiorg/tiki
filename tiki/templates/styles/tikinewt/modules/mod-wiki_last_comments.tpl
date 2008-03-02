{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-wiki_last_comments.tpl,v 1.10.2.2 2008/02/08 23:13:18 sylvieg *}

{if $prefs.feature_wiki eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` wiki comments{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last wiki comments{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="wiki_last_comments" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$comments}
      <li>
          <a class="linkmodule" href="{$comments[ix].page|sefurl}&amp;comzone=show#comments" title="{$comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$comments[ix].user}{if $moretooltips eq 'y'}{tr} on page {/tr}{$comments[ix].page}{/if}">
            {if $moretooltips ne 'y'}<b>{$comments[ix].page|escape}:</b>{/if} {$comments[ix].title|escape}
          </a>
        </li>
    {/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
