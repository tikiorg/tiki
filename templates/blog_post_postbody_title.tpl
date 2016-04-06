{* $Id$ *}
<header class="clearfix panel-heading">
	<div class="blog-postbody-title">
		{if $blog_post_context eq 'view_blog'}
			<h2 class="panel-title">
				{object_link type="blog post" id=$post_info.postId title=$post_info.title}{if $post_info.priv eq 'y'} <span class="label label-warning">{tr}private{/tr}</span>{/if}
				{include file='blog_post_actions.tpl'}
			</h2>
		{elseif $blog_post_context eq 'excerpt'}
			<bold>{object_link type="blog post" id=$post_info.postId title=$post_info.title}</bold>
		{else}
			<h2 class="panel-title">
				{object_link type="blog post" id=$post_info.postId title=$post_info.title}{if $post_info.priv eq 'y'} <span class="label label-warning">{tr}private{/tr}</span>{/if}
				<a aria-hidden="true" class="tiki_anchor" href="{$post_info.postId|sefurl:blogpost}" title="{tr}permanent link{/tr}">{icon name="link"}</a>
				{include file='blog_post_actions.tpl'}
			</h2>
		{/if}
		{include file='blog_post_author_info.tpl'}
	</div>
	{if $blog_post_context eq 'preview'}
		{include file='freetag_list.tpl' freetags=$post_info.freetags links_inactive='y'}
	{else}
		{include file='freetag_list.tpl' freetags=$post_info.freetags}
	{/if}
</header>
