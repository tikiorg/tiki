{* $Id$ *}

{if $prefs.feature_forums eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` topics{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top topics{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="forums_best_voted_topics" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
   {section name=ix loop=$modForumsTopTopics}
		<li><a class="linkmodule" href="{$modForumsTopTopics[ix].href}">
           {$modForumsTopTopics[ix].name}</a>
		</li>
   {/section}
   {if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
