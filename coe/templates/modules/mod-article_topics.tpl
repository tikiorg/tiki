{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="article_topics" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
		{section name=ix loop=$listTopics}
			<li>
				<a class="linkmodule" href="tiki-view_articles.php?topic={$listTopics[ix].topicId}">{$listTopics[ix].name|escape}</a>
			</li>
		{/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
