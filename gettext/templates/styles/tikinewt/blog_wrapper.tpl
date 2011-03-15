<div class="postbody clearfix">
	<a name="postId{$post_info.postId}"></a>
	<div class="blog_r">
		{include file='blog_post_postbody_title.tpl'}
		{include file='blog_post_postbody_content.tpl'}
		{include file='blog_post_status.tpl'}
		{include file='blog_post_navigation.tpl'}
	</div>
	<div class="blog_l">
		{include file='blog_post_author_info.tpl'}
		{if $blog_post_context ne 'print'}
			{include file='blog_post_author_actions.tpl'}
			{include file='blog_post_actions.tpl'}
		{/if}
	</div>
</div> <!-- postbody -->
