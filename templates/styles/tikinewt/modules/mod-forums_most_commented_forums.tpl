{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_most_commented_forums.tpl,v 1.12 2007/10/14 17:51:00 mose *}

{if $prefs.feature_forums eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="`$module_rows` {tr}Most commented forums{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Most commented forums{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="forums_most_commented_forums" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modForumsMostCommentedForums}
      <li>
	  <a class="linkmodule" href="{$modForumsMostCommentedForums[ix].href}">
            {$modForumsMostCommentedForums[ix].name}
          </a>
        </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
