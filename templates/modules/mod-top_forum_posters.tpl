{tikimodule error=$module_params.error title=$tpl_module_title name="top_forum_posters" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modTopForumPosters nonums=$nonums}
	{section name=ix loop=$modTopForumPosters}
		<li style="text-align: center; list-style-position: outside; margin: 0 .4em .8em 1em; padding-left: 0">
			<div style="position: relative">
			<div class="module" style="position: absolute; right: 0; bottom: -.4em;">{$modTopForumPosters[ix].posts}</div>
			<span class="module">{$modTopForumPosters[ix].name|avatarize}</span>
			<div class="module" style="position: absolute; left: 0; bottom: -.4em">{$modTopForumPosters[ix].name|escape}</div><br class="clear" /></div>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
