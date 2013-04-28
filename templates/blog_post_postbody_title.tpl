{* $Id$ *}
<header class="clearfix postbody-title">
	<div class="title">
		{if $blog_post_context eq 'view_blog'}
			<h2>{object_link type="blog post" id=$post_info.postId title=$post_info.title}{if $post_info.priv eq 'y'} ({tr}private{/tr}){/if}</h2>
		{elseif $blog_post_context eq 'excerpt'}
			<bold>{object_link type="blog post" id=$post_info.postId title=$post_info.title}</bold>
		{else}
			<h2>{$post_info.title|escape} {if $post_info.priv eq 'y'} ({tr}private{/tr}){/if}</h2>
		{/if}
	</div>
	{if $blog_post_context eq 'preview'}
		{include file='freetag_list.tpl' freetags=$post_info.freetags links_inactive='y'}
	{else}
		{include file='freetag_list.tpl' freetags=$post_info.freetags}
	{/if}
</header>
