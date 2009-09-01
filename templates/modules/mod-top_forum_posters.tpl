{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="top_forum_posters" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modTopForumPosters}
<li>
	<div class="module" style="float:left; width:50px">{$modTopForumPosters[ix].name|avatarize}</div>
	<div class="module" style="float:left">{$modTopForumPosters[ix].name|escape}</div>
	<div class="module" style="float:left;width:20px">{$modTopForumPosters[ix].posts}</div>
</li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
