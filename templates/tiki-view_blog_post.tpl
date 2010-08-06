{if strlen($blog_data.post_heading) > 0 and $prefs.feature_blog_heading eq 'y'}
  {eval var=$blog_data.post_heading}
{else}
	{include file='blog-post-heading.tpl'}
{/if}

<div class="post post_single">
	<div class="postbody">
		{include file='blog_post_actions.tpl'}
		{include file='blog_post_postbody_title.tpl'}
		{include file='blog_post_author_info.tpl'}
		{include file='blog_post_postbody_content.tpl'}
	</div>
	{include file='blog_post_footer.tpl'}
</div>

