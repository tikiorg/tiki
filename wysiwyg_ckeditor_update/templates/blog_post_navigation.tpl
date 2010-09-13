{if $blog_post_context eq 'view_blog_post' }
	<div class="postfooter-nav clearfix">
		{if $post_info.adjacent.prev}
			<div class="postfooter-nav-prev">
				<a href="{$post_info.adjacent.prev.postId|sefurl:blogpost}" title="{tr}Previous post{/tr}">&larr; {$post_info.adjacent.prev.title|truncate}</a>
			</div>
		{/if}
		{if $post_info.adjacent.next}
			<div class="postfooter-nav-next">
				<a href="{$post_info.adjacent.next.postId|sefurl:blogpost}" title="{tr}Next post{/tr}">{$post_info.adjacent.next.title|truncate} &rarr;</a>
			</div>
		{/if}
	</div>
{/if}
