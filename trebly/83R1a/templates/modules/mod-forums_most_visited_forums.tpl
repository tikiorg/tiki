{* $Id: mod-forums_most_visited_forums.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="forums_most_visited_forums" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modForumsMostVisitedForums nonums=$nonums}
	{section name=ix loop=$modForumsMostVisitedForums}
		<li>
			<a class="linkmodule" href="{$modForumsMostVisitedForums[ix].href}">
				{$modForumsMostVisitedForums[ix].name|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
