{tikimodule error=$module_params.error title=$tpl_module_title name="most_commented" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<ol>
	{section name=ix loop=$modMostCommented}
		{if $modContentType eq 'article'}
			<li><a href="tiki-read_article.php?articleId={$modArticlesMostCommented[ix].articleId}">{$modArticlesMostCommented[ix].title}</a></li>
		{/if}
		
		{if $modContentType eq 'blog'}
			<li><a href="tiki-view_blog_post.php?postId={$modBlogPostsMostCommented[ix].postId}">{$modBlogPostsMostCommented[ix].title}</a></li>
		{/if}
		
		{if $modContentType eq 'wiki'}
			<li><a href="tiki-index.php?page={$modWikiMostCommented[ix].pageName}">{$modWikiMostCommented[ix].pageName}</a></li>
		{/if}
		
	{/section}
	</ol>
{/tikimodule}