{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-last_blog_posts.tpl,v 1.2 2004-01-16 18:35:52 musus Exp $ *}

{if $feature_blogs eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` blog posts{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last blog posts{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_blog_posts"}
  <table border="0" cellpadding="0" cellspacing="0">
  {section name=ix loop=$modLastBlogPosts}
    <tr class="module">
       {if $nonums != 'y'}<td valign="top">{$smarty.section.ix.index_next})</td>{/if}
       <td>
         <a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastBlogPosts[ix].blogId}">
           <b>{$modLastBlogPosts[ix].blogTitle}:</b><br />
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
