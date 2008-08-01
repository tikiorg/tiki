{if strlen($heading) > 0}
{eval var=$heading}
{else}
{include file="blog-heading.tpl"}
{/if}
{if $use_find eq 'y'}
<div class="blogtools">
<form action="tiki-view_blog.php" method="get">
<div>
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="blogId" value="{$blogId|escape}" />
{tr}Find:{/tr} <input type="text" name="find" value="{$find|regex_replace:"/\"/":"'"}" /> <input type="submit" name="search" value="{tr}Find{/tr}" />
</div>
</form>
<!--
	{tr}Sort posts by:{/tr}
	<a class="bloglink" href="tiki-view_blog.php?blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a>
-->	
</div>
{/if}
{section name=ix loop=$listpages}
<div class="blogpost">
<div class="posthead">
{if $use_title eq 'y'}
	<h2>{$listpages[ix].title}</h2>
{else}
	<h2>{$listpages[ix].created|tiki_short_datetime}</h2>
{/if}
</div>
<div style="float:right">
{if ($ownsblog eq 'y') or ($user and $listpages[ix].user eq $user) or $tiki_p_blog_admin eq 'y'}
<a class="blogt" href="tiki-blog_post.php?blogId={$listpages[ix].blogId}&amp;postId={$listpages[ix].postId}"><img style="border:0;" src='pics/icons/page_edit.png' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' width='16' height='16' /></a> &nbsp;
<a class="blogt" href="tiki-view_blog.php?blogId={$blogId}&amp;remove={$listpages[ix].postId}"><img src='pics/icons/cross.png' alt='{tr}Remove{/tr}' style="border:0;" title='{tr}Remove{/tr}' width='16' height='16' /></a>
{/if}
{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a title="{tr}Save to notepad{/tr}" href="tiki-view_blog.php?blogId={$blogId}&amp;savenotepad={$listpages[ix].postId}"><img src='pics/icons/disk.png' style="border:0;" alt='{tr}Save{/tr}' width='16' height='16' /></a>
{/if}
</div>
<div class="freetaglist">
  {foreach from=$listpages[ix].freetags.data item=taginfo}
	  <a class="freetag" href="tiki-browse_freetags.php?tag={$taginfo.tag}">{$taginfo.tag}</a> 
  {/foreach}
</div>
<div class="postinfo">
{if $use_title eq 'y'}
	<small> {tr}Posted by{/tr} {$listpages[ix].user|userlink}  
	{if $show_avatar eq 'y'}
       {$listpages[ix].avatar}
     {/if} 
	 {tr}on{/tr} {$listpages[ix].created|tiki_short_datetime}</small>
{else}
  <small> {tr}Posted by{/tr} {$listpages[ix].user} 
	{if $show_avatar eq 'y'}
        {$listpages[ix].avatar}
    {/if}
  </small>
{/if}
</div>
<div class="postbody">
{$listpages[ix].parsed_data}
{if $listpages[ix].pages > 1}
<a class="link" href="tiki-view_blog_post.php?blogId={$blogId}&amp;postId={$listpages[ix].postId}">{tr}read more{/tr} ({$listpages[ix].pages} {tr}pages{/tr})</a>
{/if}
</div>
<div class="postfooter">
<small>
<a class="link" href="tiki-view_blog_post.php?blogId={$blogId}&amp;postId={$listpages[ix].postId}">{tr}Permalink{/tr}</a>
{* not deleted, but only commented out cause we hope to get trackbacks back into Tiki - amette 2008-04-03 *}
{* ({tr}referenced by{/tr}: {$listpages[ix].trackbacks_from_count} {tr}Posts{/tr} / {tr}references{/tr}: {$listpages[ix].trackbacks_to_count} {tr}Posts{/tr}) *}
{if $allow_comments eq 'y' and $prefs.feature_blogposts_comments eq 'y'}
{$listpages[ix].comments} {tr}comments{/tr}
 [<a class="link" href="tiki-view_blog_post.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;postId={$listpages[ix].postId}&amp;show_comments=1">{tr}View Comments{/tr}</a>]
{/if}
</small>
<a href='tiki-print_blog_post.php?postId={$listpages[ix].postId}'><img src='pics/icons/printer.png' style="border:0;" alt='{tr}Print{/tr}' title='{tr}Print{/tr}' width='16' height='16' /></a>
<a href='tiki-send_blog_post.php?postId={$listpages[ix].postId}'><img src='pics/icons/email.png' style="border:0;" alt='{tr}Email This Post{/tr}' title='{tr}Email This Post{/tr}' width='16' height='16' /></a>
</div>
</div>
{/section}
<br />
<div style="align:center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="blogprevnext" href="tiki-view_blog.php?find={$find}&amp;blogId={$blogId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
[<a class="blogprevnext" href="tiki-view_blog.php?find={$find}&amp;blogId={$blogId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-view_blog.php?find={$find}&amp;blogId={$blogId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>
{/section}
{/if}
</div>
</div>

{if $prefs.feature_blog_comments == 'y'
  && (($tiki_p_read_comments  == 'y'
  && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
<div id="page-bar">
<div class="button2">
      <a href="#comments" onclick="javascript:flip('comzone{if $comments_show eq 'y'}open{/if}');" class="linkbut">
	{if $comments_cant == 0}
          {tr}Add Comment{/tr}
        {elseif $comments_cant == 1}
          <span class="highlight">{tr}1 comment{/tr}</span>
        {else}
          <span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
        {/if}
      </a>
</div>
</div>
{include file=comments.tpl}
{/if}
