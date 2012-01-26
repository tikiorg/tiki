{* $Id$ *}
<div class="postbody clearfix">
	<a name="postId{$post_info.postId}"></a>
	{include file='blog_post_postbody_title.tpl'}
	{include file='blog_post_author_info.tpl'}
	{include file='blog_post_postbody_content.tpl'}
	<div class="postfooter">
		{if $blog_post_context ne 'print'}
			{include file='blog_post_author_actions.tpl'}
			{include file='blog_post_actions.tpl'}
			{include file='blog_post_status.tpl'}
		{/if}
	</div>
	{include file='blog_post_navigation.tpl'}
</div> <!-- postbody -->
