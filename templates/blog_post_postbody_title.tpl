{* $Id$ *}
<header class="clearfix postbody-title panel-heading">
	<div class="title">
		{if $blog_post_context eq 'view_blog'}
			<h2 class="panel-title">{object_link type="blog post" id=$post_info.postId title=$post_info.title}{if $post_info.priv eq 'y'} ({tr}private{/tr}){/if}</h2>
		{elseif $blog_post_context eq 'excerpt'}
			<bold>{object_link type="blog post" id=$post_info.postId title=$post_info.title}</bold>
		{else}
			<h2 class="panel-title">{$post_info.title|escape} {if $post_info.priv eq 'y'} ({tr}private{/tr}){/if}<a aria-hidden="true" class="tiki_anchor" href="{$post_info.postId|sefurl:blogpost}" title="{tr}permanent link{/tr}">{icon name="link"}</a></h2>
		{/if}
	</div>
	{if $blog_post_context eq 'preview'}
		{include file='freetag_list.tpl' freetags=$post_info.freetags links_inactive='y'}
	{else}
		{include file='freetag_list.tpl' freetags=$post_info.freetags}
	{/if}
</header>
