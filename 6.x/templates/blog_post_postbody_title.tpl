{* $Id$ *}
<div class="clearfix postbody-title">
	<div class="title">
		{if $blog_post_context eq 'view_blog'}
			<h2><a class="link" href="{$post_info.postId|sefurl:blogpost}">{$post_info.title|escape}</a></h2>
		{else}
			<h2>{$post_info.title|escape}</h2>
		{/if}
	</div>
	{if $blog_post_context eq 'preview' }
		{include file='freetag_list.tpl' freetags=$post_info.freetags links_inactive='y'}
	{else}
		{include file='freetag_list.tpl' freetags=$post_info.freetags}
	{/if}
</div>
