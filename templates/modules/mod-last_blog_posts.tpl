{* $Id$ *}

{if $prefs.feature_blogs eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` blog posts{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last blog posts{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_blog_posts" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
  {section name=ix loop=$modLastBlogPosts}
    <li>
        <a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastBlogPosts[ix].blogId}" title="{$modLastBlogPosts[ix].created|tiki_short_datetime}, {tr}by{/tr} {if $modLastBlogPosts[ix].user ne ''}{$modLastBlogPosts[ix].user}{else}{tr}Anonymous{/tr}{/if}">
           <b>{$modLastBlogPosts[ix].blogTitle}</b>: {$modLastBlogPosts[ix].title}<br />
           {if $modLastBlogPostsTitle eq "title" and $modLastBlogPosts[ix].title}
             {$modLastBlogPosts[ix].title}
           {else}
            <span style="font-weight:normal;font-style:italic">{$modLastBlogPosts[ix].created|tiki_short_datetime}</span>
           {/if}
         </a>
       </li>
  {/section}
 {if $nonums != 'y'}</ol>{else}</ul>{/if} 
{/tikimodule}
{/if}
