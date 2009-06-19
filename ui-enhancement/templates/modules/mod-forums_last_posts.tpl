{* $Header$ *}

{if $prefs.feature_forums eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` forum posts{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last forum posts{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="forums_last_posts" flip=$module_params.flip decorations=$module_params.decorations notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modForumsLastPosts}
<li><a class="linkmodule" href="{$modForumsLastPosts[ix].href}" title="{$modForumsLastPosts[ix].date|tiki_short_datetime}, {tr}by{/tr} {if $modForumsLastPosts[ix].user ne ''}{$modForumsLastPosts[ix].user}{else}{tr}Anonymous{/tr}{/if}">
            {$modForumsLastPosts[ix].name}
          </a>
</li>        
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
