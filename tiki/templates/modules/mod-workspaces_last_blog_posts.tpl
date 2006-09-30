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
{tiki_workspaces_module title=$tpl_module_title name="workspaces_last_blog_posts" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}
{if $showBar!='n'}
<div class="resourceSelect">
<form name="blogsSelection" method="post" action="{$ownurl}">
  <input name="moduleId" type="hidden" id="moduleId" value="{$moduleId}">
  <label for="name">{tr}Blogs{/tr}:</label>
  <select name="name" id="name">
  {foreach key=key item=workspaceBlog from=$workspaceBlogs}
  	<option value="{$workspaceBlog.name}" {if $selectedBlogId==$workspaceBlog.objId}selected{/if}>
  	{$workspaceBlog.name}</option>
      {/foreach}
  </select>
  <input class="edubutton" type="submit" name="go" value="{tr}Go{/tr}">
</form>
</div>
{/if}
<div class="wsBlogTitle">
	({$selectedBlog.name}) {$selectedBlog.description}
</div>
  {section name=ix loop=$modLastBlogPosts}
       <div class="wsblogpost">
       {if $nonums != 'y'}
       {$smarty.section.ix.index_next})
       {/if}
         
         <div class="wsBlogPostTitle">{$modLastBlogPosts[ix].title}</div>
       {if $body == 'y'}         
         {*<a id="flipperidblogpost{$smarty.section.ix.index_next}" class="link" href="javascript:flipWithSign('idblogpost{$smarty.section.ix.index_next}')">[+]</a>*}
         <div id="idblogpost{$smarty.section.ix.index_next}">
          {$modLastBlogPosts[ix].data}
         </div>
         <div class="wsBlogPostInfo">
	         <a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastBlogPosts[ix].blogId}">
	             {$modLastBlogPosts[ix].created|tiki_short_datetime}- {tr}by{/tr} {$modLastBlogPosts[ix].user}
	         </a>
         </div>
       {/if}
       </div>
  {/section}
<div class="mini">
	{if $prev_offset >= 0}
	<a class="prevnext" href="{$ownurl}&amp;offset={$prev_offset}"><img src="pics/icons/resultset_previous.png" border="0" alt="{tr}prev posts{/tr}" width='16' height='16' /></a>
	{/if}
	{$actual_page}/{$cant_pages}
	{if $next_offset >= 0}
	<a class="prevnext" href="{$ownurl}&amp;offset={$next_offset}"><img src="pics/icons/resultset_next.png" border="0" alt="{tr}next posts{/tr}" width='16' height='16' /></a>
	{/if}
</div>
{/tiki_workspaces_module}
{/if}
