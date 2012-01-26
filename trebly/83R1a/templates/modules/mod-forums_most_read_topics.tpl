{* $Id: mod-forums_most_read_topics.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="forums_most_read_topics" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modForumsMostReadTopics nonums=$nonums}
	{section name=ix loop=$modForumsMostReadTopics}
		<li>
			<a class="linkmodule" href="{$modForumsMostReadTopics[ix].href}">
				{$modForumsMostReadTopics[ix].name|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
