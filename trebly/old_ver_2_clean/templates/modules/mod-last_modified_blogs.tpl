{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_modified_blogs" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modLastModifiedBlogs nonums=$nonums}
	{section name=ix loop=$modLastModifiedBlogs}
		<li>
			<a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastModifiedBlogs[ix].blogId}">
				{$modLastModifiedBlogs[ix].title|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
