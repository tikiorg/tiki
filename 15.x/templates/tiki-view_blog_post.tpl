{if strlen($blog_data.post_heading) > 0 and $prefs.feature_blog_heading eq 'y'}
	{eval var=$blog_data.post_heading}
{else}
	{include file='blog_post_heading.tpl'}
{/if}

{* Blog comment mail *}
<div class="clearfix">
    <div class="blogactions pull-right margin-bottom-md">
	    {if $user and $prefs['feature_blogs'] eq 'y'}
		    {if $user_watching_blog eq 'n'}
			    <a href="tiki-view_blog_post.php?postId={$postId}&amp;watch_event=blog_comment_changes&amp;watch_object={$postId}&amp;watch_action=add" class="tips" title=":{tr}Monitor this Blog{/tr}">{icon name="watch"}</a>
		    {else}
			    <a href="tiki-view_blog_post.php?postId={$postId}&amp;watch_event=blog_comment_changes&amp;watch_object={$postId}&amp;watch_action=remove" class="tips" title=":{tr}Stop Monitoring this Blog{/tr}">{icon name="stop-watching"}</a>
		    {/if}
	    {/if}
			{if $tiki_p_blog_post eq "y" or $tiki_p_blog_admin eq "y" }
					<a href="tiki-blog_post.php?blogId={$blogId}" class="tips" title=":{tr}Post{/tr}" >
						{icon name='post'}
					</a>
			{/if}
    </div>
</div>

<article class="blogpost post post_single panel panel-default">
	{include file='blog_wrapper.tpl' blog_post_context='view_blog_post'}
</article>
{include file='blog_post_related_content.tpl'}

{if $prefs.feature_blogposts_comments == 'y' && $blog_data.allow_comments == 'y'
		&& ($tiki_p_read_comments == 'y'
			|| $tiki_p_post_comments == 'y'
			|| $tiki_p_edit_comments == 'y')}
	<div id="comment-container" data-target="{service controller=comment action=list type="blog post" objectId=$postId}"></div>
	{jq}
		var id = '#comment-container';
		$(id).comment_load($(id).data('target'));
		$(document).ajaxComplete(function(){$(id).tiki_popover();});
	{/jq}
{/if}
