<h1><a href="tiki-view_blog_post.php?blogId={$post_info.blogId}&amp;postId={$post_info.postId}" class="pagetitle">{tr}Viewing blog post{/tr} - {$blog_data.title}</a></h1>
<span class="button2"><a class="linkbut" href="tiki-view_blog.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}">{tr}Return to blog{/tr}</a></span>
<br /><br />
<div class="posthead">
{if $blog_data.use_title eq 'y'}
	<h3>{$post_info.title}</h3>
{else}
	<h3>{$post_info.created|tiki_short_datetime}</h3>
{/if}
{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
<div class="freetaglist">
  {foreach from=$tags.data item=tag}
	<a class="freetag" href="tiki-browse_freetags.php?tag={$tag.tag}">{$tag.tag}</a> 
  {/foreach}
</div>
{/if}
<table ><tr><td align="left">
<span class="posthead">
{if $blog_data.use_title eq 'y'}
	<small> {tr}posted by{/tr} {$post_info.user} {tr}on{/tr} {$post_info.created|tiki_short_datetime}</small>
{else}
	<small> {tr}posted by{/tr} {$post_info.user}</small>
{/if}
</span>
</td><td align="right">
{if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
<a class="blogt" href="tiki-blog_post.php?blogId={$post_info.blogId}&amp;postId={$post_info.postId}">{icon _id='page_edit'}</a>
<a class="blogt" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;remove={$post_info.postId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
{/if}
{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a title="{tr}Save to notepad{/tr}" href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;savenotepad=1">{icon _id='disk' alt='{tr}Save{/tr}'}</a>
{/if}
</td></tr></table>
</div>
<div class="postbody">
{$parsed_data}
{if $pages > 1}
	<div align="center">
		<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$first_page}">{icon _id='resultset_first' alt='{tr}First page{/tr}'}</a>
		<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$prev_page}">{icon _id='resultset_previous' alt='{tr}Previous page{/tr}'}</a>
		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>
		<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$next_page}">{icon _id='resultset_next' alt='{tr}Next page{/tr}'}</a>
		<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$last_page}">{icon _id='resultset_last' alt='{tr}Last page{/tr}'}</a>
	</div>
{/if}

{if $prefs.blogues_feature_copyrights  eq 'y' and $prefs.wikiLicensePage}
  {if $prefs.wikiLicensePage == $page}
    {if $tiki_p_edit_copyrights eq 'y'}
      <p class="editdate">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.</p>
    {/if}
  {else}
    <p class="editdate">{tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$prefs.wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.</p>
  {/if}
{/if}
<hr/>
<table >
<tr><td>
<small>
<a class="link" href="tiki-view_blog_post.php?blogId={$blogId}&amp;postId={$postId}">{tr}Permalink{/tr}</a>
({tr}referenced by{/tr}: {$post_info.trackbacks_from_count} {tr}Posts{/tr} {tr}references{/tr}: {$post_info.trackbacks_to_count} {tr}Posts{/tr})
{if $allow_comments eq 'y' and $prefs.feature_blogposts_comments eq 'y'}
{$listpages[ix].comments} {tr}comments{/tr}
 [<a class="link" href="tiki-view_blog_post.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;postId={$listpages[ix].postId}">{tr}View Comments{/tr}</a>]
{/if}
</small>
</td><td style='text-align:right'>
<a href='tiki-print_blog_post.php?postId={$postId}'>{icon _id='printer' alt='{tr}Print{/tr}'}</a>
<a href='tiki-send_blog_post.php?postId={$postId}'>{icon _id='email' alt='{tr}email this post{/tr}'}</a>
</td></tr></table>
</div>
{if $post_info.trackbacks_from_count > 0}
<h3>{tr}Trackback pings{/tr}:</h3>
{cycle values="odd,even" print=false}
<table class="normal">
<tr>
	<td class="heading">{tr}Title{/tr}</td>
	<td class="heading">{tr}URI{/tr}</td>
	<td class="heading">{tr}Blog name{/tr}</td>
{if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
	<td class="heading">{tr}Action{/tr}</td>
{/if}
</tr>
{foreach from=$post_info.trackbacks_from key=key item=item}
<tr>
  <td  class="{cycle advance=false}">{$item.title|htmlentities}</td>
  <td  class="{cycle advance=false}"><a href="{$key}" class="link" title="{$key}" target="_blank">{$key|truncate:"40"|htmlentities}</a></td>
  <td  class="{cycle}">{$item.blog_name|htmlentities}</td>
  {if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
    <td  class="{cycle advance=false}"><a href="tiki-view_blog_post.php?postId={$postId}&amp;deltrack={$key|urlencode}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a></td>
  {/if}
</tr>
{/foreach}
</table>
{/if}

{if $prefs.feature_blogposts_comments == 'y'
  && ($blog_data.allow_comments == 'y' or $blog_data.allow_comments == 'c')
  && (($tiki_p_read_comments  == 'y'
  && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
<div id="page-bar">
<table>
<tr><td>
<div class="button2">
<a href="#comment" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}Add Comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if (isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y') or $show_comments}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
</div>
</td></tr></table>
</div>
{include file=comments.tpl}
{/if}
