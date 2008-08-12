{* $Id$ *}

{if $prefs.feature_blogs eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Most `$module_rows` visited blogs{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Most visited blogs{/tr}" assign="tpl_module_title"}
{/if}
{/if}

    {tikimodule title=$tpl_module_title name="top_visited_blogs" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modTopVisitedBlogs}
	<li>
	<a class="linkmodule" href="tiki-view_blog.php?blogId={$modTopVisitedBlogs[ix].blogId}">{$modTopVisitedBlogs[ix].title}</a></li>
    {/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
    {/tikimodule}
{/if}
