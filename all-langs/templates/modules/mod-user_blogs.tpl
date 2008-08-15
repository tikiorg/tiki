{* $Id$ *}

{if $user}
    {if $prefs.feature_blogs eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}My blogs{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="user_blogs" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
	{section name=ix loop=$modUserBlogs}
	    <li>
		<a class="linkmodule" href="tiki-view_blog.php?blogId={$modUserBlogs[ix].blogId}">{$modUserBlogs[ix].title}</a></li>
	{/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
	{/tikimodule}
    {/if}
{/if}
