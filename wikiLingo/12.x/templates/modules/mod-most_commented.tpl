{tikimodule error=$module_error title=$tpl_module_title name="most_commented" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modMostCommented nonums=$nonums}
	{section name=ix loop=$modMostCommented}
		{if $modContentType eq 'article'}
			<li><a href="tiki-read_article.php?articleId={$modMostCommented[ix].articleId}">{$modMostCommented[ix].title|escape}</a></li>
		{/if}
		
		{if $modContentType eq 'blog'}
			<li><a href="tiki-view_blog_post.php?postId={$modMostCommented[ix].postId}">{$modMostCommented[ix].title|escape}</a></li>
		{/if}
		
		{if $modContentType eq 'wiki'}
			<li><a href="tiki-index.php?page={$modMostCommented[ix].pageName|escape:url}">{$modMostCommented[ix].pageName|escape}</a></li>
		{/if}
	{/section}
{/modules_list}
{/tikimodule}
