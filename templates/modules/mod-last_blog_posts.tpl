{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_blog_posts.tpl,v 1.9 2003-11-24 01:33:46 zaufi Exp $ *}

{if $feature_blogs eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` blog posts{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last blog posts{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_blog_posts"}
  <table  border="0" cellpadding="0" cellspacing="0">
  {section name=ix loop=$modLastBlogPosts}
    <tr>
       {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
       <td class="module">
         <a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastBlogPosts[ix].blogId}">
           <b>{$modLastBlogPosts[ix].blogTitle}:</b><br/>
           {if $modLastBlogPostsTitle eq "title" and $modLastBlogPosts[ix].title}
             {$modLastBlogPosts[ix].title}
           {else}
             {$modLastBlogPosts[ix].created|tiki_short_datetime}
           {/if}
         </a>
       </td>
     </tr>
  {/section}
  </table>
{/tikimodule}
{/if}
