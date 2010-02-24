
{tikimodule error=$module_params.error title=$tpl_module_title name="wiki_most_commented" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{section name=ix loop=$modWikiMostCommented}
		
		<li><a href="tiki-index.php?page={$modWikiMostCommented[ix].pageName}">{$modWikiMostCommented[ix].pageName}</a></li>
	
	{/section}
	
{/tikimodule}