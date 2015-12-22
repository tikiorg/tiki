{if $blog_post_context eq 'view_blog_post'}
	<ul class="pager">
		{if $post_info.adjacent.prev}
			<li class="previous">
				{self_link _script=$post_info.adjacent.prev.postId|sefurl:blogpost _title="{tr}Previous post{/tr}" _noauto='y'}&larr; {$post_info.adjacent.prev.title|truncate}{/self_link}
			</li>
		{/if}
		{if $post_info.adjacent.next}
			<li class="next">
				{self_link _script=$post_info.adjacent.next.postId|sefurl:blogpost _title="{tr}Next post{/tr}" _noauto='y'}{$post_info.adjacent.next.title|truncate} &rarr;{/self_link}
			</li>
		{/if}
	</ul>
{/if}
