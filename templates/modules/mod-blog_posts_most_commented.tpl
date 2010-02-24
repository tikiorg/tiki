
{tikimodule error=$module_params.error title=$tpl_module_title name="blog_posts_most_commented" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{section name=ix loop=$modBlogPostsMostCommented}
		
		<li><a href="tiki-view_blog_post.php?postId={$modBlogPostsMostCommented[ix].postId}">{$modBlogPostsMostCommented[ix].title}</a></li>
	
	{/section}
	
{/tikimodule}