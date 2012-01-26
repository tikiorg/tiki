{* $Id: blog_wrapper.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
<div class="postbody clearfix">
	<a name="postId{$post_info.postId}"></a>
	{include file='blog_post_postbody_title.tpl'}
	{include file='blog_post_author_info.tpl'}
	{include file='blog_post_postbody_content.tpl'}
	<div class="postfooter">
		{if $blog_post_context ne 'print'}
			{*include file='blog_post_author_actions.tpl'*} {* moved to blog_post_author_info.tpl *}
			{include file='blog_post_actions.tpl'}
			{include file='blog_post_status.tpl'}
		{/if}
	</div>
	{include file='blog_post_navigation.tpl'}
</div> <!-- postbody -->
