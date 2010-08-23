{if strlen($blog_data.post_heading) > 0 and $prefs.feature_blog_heading eq 'y'}
  {eval var=$blog_data.post_heading}
{else}
	{include file='blog-post-heading.tpl'}
{/if}

<div class="post post_single">
	<div class="postbody clearfix">
		{include file='blog_post_actions.tpl'}
		{include file='blog_post_postbody_title.tpl'}
		{include file='blog_post_author_info.tpl'}
		{include file='blog_post_postbody_content.tpl'}
		{include file='blog_post_footer.tpl'}
	</div>
	{include file='blog_post_related_content.tpl'}
</div>

{if $prefs.feature_blogposts_comments == 'y'
		&& ($blog_data.allow_comments == 'y' or $blog_data.allow_comments == 'c')
		&& (($tiki_p_read_comments == 'y' && $comments_cant != 0)
			|| $tiki_p_post_comments == 'y'
			|| $tiki_p_edit_comments == 'y')}
	{assign var='show_comzone' value='y'}
	{include file='comments.tpl'}
{/if}
