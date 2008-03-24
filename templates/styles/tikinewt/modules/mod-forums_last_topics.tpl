{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_last_topics.tpl,v 1.15 2007/10/14 17:51:00 mose *}

{if $prefs.feature_forums eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` forum topics{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last forum topics{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="forums_last_topics" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modForumsLastTopics}
      <li>
	  	{if $absurl == 'y'}
          <a class="linkmodule" href="{$feature_server_name}{$modForumsLastTopics[ix].href}" title="{$modForumsLastTopics[ix].date|tiki_short_datetime}, {tr}by{/tr} {if $modForumsLastTopics[ix].user ne ''}{$modForumsLastTopics[ix].user}{else}{tr}Anonymous{/tr}{/if}">
            {$modForumsLastTopics[ix].name}
          </a>
		{else}
          <a class="linkmodule" href="{$modForumsLastTopics[ix].href}" title="{$modForumsLastTopics[ix].date|tiki_short_datetime}, {tr}by{/tr} {if $modForumsLastTopics[ix].user ne ''}{$modForumsLastTopics[ix].user}{else}{tr}Anonymous{/tr}{/if}">
            {$modForumsLastTopics[ix].name}
          </a>
		{/if}
        </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
