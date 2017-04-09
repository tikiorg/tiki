{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="top_blog_posters" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modTopBloggers nonums=$nonums}
	{section name=ix loop=$modTopBloggers}
		<li>
				{$modTopBloggers[ix].user|userlink}
		</li>
	{/section}
{/modules_list}
{/tikimodule}
