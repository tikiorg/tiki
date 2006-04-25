{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{if $feature_blogs eq 'y'}
{if $title==""}
{assign var=title value="{tr}Workspace History{/tr}"}
{/if}
{eval var="{tr}$title{/tr}" assign="tpl_module_title"}
{tikimodule title=$tpl_module_title name="aulawiki_last_blog_posts" flip=$module_params.flip decorations=$module_params.decorations}
{include file="aulawiki-module_error.tpl" error=$error_msg}
<div class="resourceSelect">
<form name="blogsSelection" method="post" action="tiki-view_blog.php">
  <label for="blogId">{tr}Blogs{/tr}:</label>
  <select name="blogId" id="blogId">
  {foreach key=key item=workspaceBlog from=$workspaceBlogs}
  	<option value="{$workspaceBlog.objId}" {if $selectedBlogId==$workspaceBlog.objId}selected{/if}>
  	{$workspaceBlog.name}</option>
      {/foreach}
  </select>
  <input class="edubutton" type="submit" name="go" value="{tr}Go{/tr}">
</form>
</div>

  <table  border="0" cellpadding="0" cellspacing="0">
  {section name=ix loop=$modLastBlogPosts}
    <tr>
    <td>
       <div class="edubox">
       {if $nonums != 'y'}
       {$smarty.section.ix.index_next})
       {/if}
         <a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastBlogPosts[ix].blogId}">
             {$modLastBlogPosts[ix].created|tiki_short_datetime}-{$modLastBlogPosts[ix].title}
         </a>
       {if $body == 'y'}         
         <a id="flipperidblogpost{$smarty.section.ix.index_next}" class="link" href="javascript:flipWithSign('idblogpost{$smarty.section.ix.index_next}')">[+]</a>
         <div id="idblogpost{$smarty.section.ix.index_next}" style="display:none;">
          {$modLastBlogPosts[ix].data}
         </div>
       {/if}
       </div>
     </td>
     </tr>
  {/section}
  </table>
{/tikimodule}
{/if}
