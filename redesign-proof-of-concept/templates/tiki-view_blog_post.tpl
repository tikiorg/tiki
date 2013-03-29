{if strlen($blog_data.post_heading) > 0 and $prefs.feature_blog_heading eq 'y'}
  {eval var=$blog_data.post_heading}
{else}
	{include file='blog_post_heading.tpl'}
{/if}

{* Blog comment mail *}
<div style="float:right;">
{if $user and $prefs['feature_blogs'] eq 'y'}
	{if $user_watching_blog eq 'n'}
				<a href="tiki-view_blog_post.php?postId={$postId}&amp;watch_event=blog_comment_changes&amp;watch_object={$postId}&amp;watch_action=add" class="icon">{icon _id='eye' alt="{tr}Monitor this Blog{/tr}"}</a>
			{else}
				<a href="tiki-view_blog_post.php?postId={$postId}&amp;watch_event=blog_comment_changes&amp;watch_object={$postId}&amp;watch_action=remove" class="icon">{icon _id='no_eye' alt="{tr}Stop Monitoring this Blog{/tr}"}</a>
	{/if}
{/if}
</div>


<article class="blogpost post post_single">
	{include file='blog_wrapper.tpl' blog_post_context='view_blog_post'}
	{include file='blog_post_related_content.tpl'}
</article>

{if $prefs.feature_blogposts_comments == 'y' && $blog_data.allow_comments == 'y'
		&& ($tiki_p_read_comments == 'y'
			|| $tiki_p_post_comments == 'y'
			|| $tiki_p_edit_comments == 'y')}
	<div id="comment-container" data-target="{service controller=comment action=list type="blog post" objectId=$postId}"></div>
	{jq}
		var id = '#comment-container';
		$(id).comment_load($(id).data('target'));
	{/jq}
{/if}
