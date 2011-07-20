{if $blog_post_context eq 'view_blog_post'}
	<div class="postfooter-nav clearfix">
		{if $post_info.adjacent.prev}
			<div class="postfooter-nav-prev">
				{self_link _script=$post_info.adjacent.prev.postId|sefurl:blogpost _title='{tr}Previous post{/tr}' _noauto='y'}&larr; {$post_info.adjacent.prev.title|truncate}{/self_link}
			</div>
		{/if}
		{if $post_info.adjacent.next}
			<div class="postfooter-nav-next">
				{self_link _script=$post_info.adjacent.next.postId|sefurl:blogpost _title='{tr}Next post{/tr}' _noauto='y'}{$post_info.adjacent.next.title|truncate} &rarr;{/self_link}
			</div>
		{/if}
	</div>
{/if}
