{* $Id$ *}
{if $blog_post_context eq 'view_blog' && $use_excerpt eq 'y' && !empty($post_info.excerpt)}
	<div class="postbody-content postbody-excerpt panel-body">
		{include file='blog_post_author_info.tpl'}
		{$post_info.parsed_excerpt}
	</div>
	{self_link _script=$post_info.postId|sefurl:blogpost _noauto='y' _class="btn btn-link"}{tr}Read more{/tr}{/self_link}
{else}
	<div class="postbody-content panel-body">
		{include file='blog_post_author_info.tpl'}
		{$post_info.parsed_data}
	</div>
{/if}

{if $post_info.pages > 1}
	<div class="postbody-pagination">
		{if $blog_post_context eq 'view_blog'}
			<a class="link more" href="{$post_info.postId|sefurl:blogpost}">
			{tr}More...{/tr} ({$post_info.pages} {tr}pages{/tr})</a>
		{else}
			<div align="center">
				<a class="tips" title=":{tr}First{/tr}" href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$post_info.first_page}">
					{icon name='backward_step'}
				</a>
				<a class="tips" title=":{tr}Previous{/tr}" href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$post_info.prev_page}">
					{icon name='backward'}
				</a>
				<small>{tr}page:{/tr}{$post_info.pagenum}/{$post_info.pages}</small>
				<a class="tips" title=":{tr}Next{/tr}" href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$post_info.next_page}">
					{icon name='forward'}
				</a>
				<a class="tips" title=":{tr}Last{/tr}" href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$post_info.last_page}">
					{icon name='forward_step'}
				</a>
			</div>
		{/if}
	</div>
{/if}
