{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-last_modified_blogs.tpl,v 1.9.10.1 2005/02/23 21:05:10 michael_davey *}

{if $feature_blogs eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Modified blogs{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Modified blogs{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_modified_blogs" flip=$module_params.flip decorations=$module_params.decorations}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modLastModifiedBlogs}
      <li>
	  	<a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastModifiedBlogs[ix].blogId}">
            {$modLastModifiedBlogs[ix].title}
          </a>
        </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
