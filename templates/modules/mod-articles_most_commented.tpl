
{tikimodule error=$module_params.error title=$tpl_module_title name="article_most_commented" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{section name=ix loop=$modArticlesMostCommented}
		
		<li><a href="tiki-read_article.php?articleId={$modArticlesMostCommented[ix].articleId}">{$modArticlesMostCommented[ix].title}</a></li>
	
	{/section}
	
{/tikimodule}