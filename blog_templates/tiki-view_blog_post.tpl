{* $Id$ *}
{if strlen($blog_data.post_heading) > 0 and $prefs.feature_blog_heading eq 'y'}
  {eval var=$blog_data.post_heading}
{else}
  {include file='tiki-blog_post_heading.tpl'}
{/if}

<div class="post">
	<div class="postbody">
		{include file='tiki-view_blog_post_actions.tpl'}{* actions split out, info moved below title for vacomm theme *}
		{include file='tiki-view_blog_post_postbody_title.tpl'}
		{include file='tiki-view_blog_post_author_info.tpl'}
		{include file='tiki-view_blog_post_postbody_content.tpl'}
	</div>
	{include file='tiki-view_blog_post_footer.tpl'}
</div>
