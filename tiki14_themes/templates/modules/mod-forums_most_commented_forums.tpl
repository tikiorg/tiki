{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="forums_most_commented_forums" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modForumsMostCommentedForums nonums=$nonums}
	{section name=ix loop=$modForumsMostCommentedForums}
		<li>
			<a class="linkmodule" href="{$modForumsMostCommentedForums[ix].href}">
				{$modForumsMostCommentedForums[ix].name|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
