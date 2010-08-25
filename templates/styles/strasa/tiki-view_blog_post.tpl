{title help="Blogs"}{$blog_data.title|escape}{/title}
<a class="link" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a> {$prefs.site_crumb_seper} <a class="link" href="tiki-view_blog.php?blogId={$post_info.blogId}">{$blog_data.title|escape}</a> {$prefs.site_crumb_seper} {$post_info.title|escape}

<div class="post">
	{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
		{if $tags.data|@count >0}
			<div class="freetaglist">
    			{foreach from=$tags.data item=tag}
					{capture name=tagurl}{if (strstr($tag.tag, ' '))}"{$tag.tag}"{else}{$tag.tag}{/if}{/capture}
					<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$tag.tag}</a> 
				{/foreach}
			</div>
		{/if}
	{/if}
	<div class="postbody">
	<div class="clearfix postbody-title">
	    {if $blog_data.show_avatar eq 'y'}
			{$post_info.avatar}
		{/if}
		
		<div class="title">		
			<h2>{$post_info.title|escape}</h2>
		</div>
	</div>
	<div class="content">
		<div class="author_actions clearfix">
			<div class="actions">
{if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
<a class="blogt" href="tiki-blog_post.php?blogId={$post_info.blogId}&amp;postId={$post_info.postId}">{icon _id='page_edit'}</a>
<a class="blogt" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;remove={$post_info.postId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
{/if}
{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a title="{tr}Save to notepad{/tr}" href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;savenotepad=1">
	{icon _id='disk' alt='{tr}Save to notepad{/tr}'}
</a>
{/if}
			</div>
		<div class="author_info">
		
			{if $blog_data.use_author eq 'y' || $blog_data.add_date eq 'y'}
			{tr}Published {/tr}
			{/if}
			
			{if $blog_data.use_author eq 'y'}
				{tr}by{/tr} {$post_info.user} 
			{/if}
			
			{if $blog_data.add_date eq 'y'}
				{tr}on{/tr} {$post_info.created|tiki_short_datetime}
			{/if}
		</div>
	</div>
</div>
<div class="postbody-content">
{$parsed_data}
</div>
{if $pages > 1}
	<div align="center">
		<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$first_page}">{icon _id='resultset_first' alt='{tr}First page{/tr}'}</a>
		<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$prev_page}">{icon _id='resultset_previous' alt='{tr}Previous page{/tr}'}</a>
		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>
		<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$next_page}">{icon _id='resultset_next' alt='{tr}Next page{/tr}'}</a>
		<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$last_page}">{icon _id='resultset_last' alt='{tr}Last page{/tr}'}</a>
	</div>
{/if}
{*</div>*}
	{if $prefs.blogs_feature_copyrights eq 'y' and $prefs.wikiLicensePage}
		{if $prefs.wikiLicensePage == $page}
    {if $tiki_p_edit_copyrights eq 'y'}
      <p class="editdate">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.</p>
    {/if}
  {else}
    <p class="editdate">{tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$prefs.wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.</p>
  {/if}
{/if}
</div>
<div class="postfooter">
	<div class="status"> {* renamed to match forum footer layout *}
		<a href='tiki-print_blog_post.php?postId={$postId}'>{icon _id='printer' alt='{tr}Print{/tr}'}</a>
		{if $prefs.feature_blog_sharethis eq "y"}
			{capture name=shared_title}{tr}Share This{/tr}{/capture}
			{capture name=shared_link_title}{tr}ShareThis via AIM, social bookmarking and networking sites, etc.{/tr}{/capture}
			{wiki}{literal}<script language="javascript" type="text/javascript">
				//Create your sharelet with desired properties and set button element to false
				var object{/literal}{$postId}{literal} = SHARETHIS.addEntry({
					title:'{/literal}{$smarty.capture.shared_title|replace:'\'':'\\\''}{literal}'
				},
				{button:false});
				//Output your customized button
				document.write('<span id="share{/literal}{$postId}{literal}"><a title="{/literal}{$smarty.capture.shared_link_title|replace:'\'':'\\\''}{literal}" href="javascript:void(0);"><img src="http://w.sharethis.com/images/share-icon-16x16.png?CXNID=1000014.0NXC" /></a></span>');
				//Tie customized button to ShareThis button functionality.
				var element{/literal}{$postId}{literal} = document.getElementById("share{/literal}{$postId}{literal}");
				object{/literal}{$postId}{literal}.attachButton(element{/literal}{$postId}{literal});
			</script>{/literal}{/wiki}
		{/if}
	</div>
	<div class="actions"> {* renamed to match forum footer layout *}
<a class="link" href="{$postId|sefurl:blogpost}">{tr}Permalink{/tr}</a>
{if $allow_comments eq 'y' and $prefs.feature_blogposts_comments eq 'y'}
{$listpages[ix].comments} {tr}comments{/tr}
 [<a class="link" href="tiki-view_blog_post.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;postId={$listpages[ix].postId}">{tr}View Comments{/tr}</a>]
{/if}
	</div>
</div>
</div>
{if $prefs.feature_blogposts_comments == 'y'
  && ($blog_data.allow_comments == 'y' or $blog_data.allow_comments == 'c')
  && (($tiki_p_read_comments == 'y'
  && $comments_cant != 0)
  || $tiki_p_post_comments == 'y'
  || $tiki_p_edit_comments == 'y')
}
  <div id="page-bar" class="clearfix">
  	{include file=comments_button.tpl}   
  </div>
  {include file=comments.tpl}
{/if}
