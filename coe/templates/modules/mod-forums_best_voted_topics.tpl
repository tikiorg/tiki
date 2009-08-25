{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="forums_best_voted_topics" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
   {section name=ix loop=$modForumsTopTopics}
		<li><a class="linkmodule" href="{$modForumsTopTopics[ix].href}">
           {$modForumsTopTopics[ix].name|escape}</a>
		</li>
   {/section}
   {if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
