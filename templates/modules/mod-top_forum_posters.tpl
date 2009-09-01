{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="top_forum_posters" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modTopForumPosters}
<li class="clearfix" style="margin-bottom: .4em; text-align: center;">
	<div class="module" style="float:right">{$modTopForumPosters[ix].posts}</div>
	<span class="module">{$modTopForumPosters[ix].name|avatarize}</span>
	<div class="module" style="float:left;">{$modTopForumPosters[ix].name|escape}</div>
</li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
