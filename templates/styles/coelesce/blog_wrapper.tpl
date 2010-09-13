{* $Id$ *}
{include file='blog_post_navigation.tpl'}
<div class="postbody clearfix">
	<a name="postId{$post_info.postId}"></a>
	{include file='blog_post_postbody_title.tpl'}
	<div class="clearfix author_actions_status">
		{include file='blog_post_author_info.tpl'}
		{include file='blog_post_author_actions.tpl'}
	</div>
	{if $blog_post_context eq 'preview' }
		{include file='freetag_list.tpl' freetags=$post_info.freetags links_inactive='y'}
	{else}
		{include file='freetag_list.tpl' freetags=$post_info.freetags}
	{/if}
	{include file='blog_post_postbody_content.tpl'}
	<div class="postfooter">
		{if $blog_post_context ne 'print'}
			{include file='blog_post_status.tpl'}
			{include file='blog_post_actions.tpl'}
		{/if}
	</div>
</div> <!-- postbody -->
