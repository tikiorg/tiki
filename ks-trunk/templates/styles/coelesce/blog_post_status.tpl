<div class="status">
	<a class="link" href="{$post_info.postId|sefurl:blogpost}">{tr}Permalink{/tr}</a>
	{if $blog_post_context eq 'view_blog'}
		{if $allow_comments eq 'y' and $prefs.feature_blogposts_comments eq 'y' && $tiki_p_read_comments eq 'y'}
			|
 			<a class="link linkcomments" href="tiki-view_blog_post.php?find={$find|escape:url}&amp;blogId={$post_info.blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;postId={$post_info.postId}#comments">
				{if $post_info.comments == 0 && $tiki_p_post_comments eq 'y'}
					{tr}Leave a comment{/tr}
				{else}
					{$post_info.comments}
					{if $post_info.comments == 1}
						{tr}comment{/tr}
					{else}
						{tr}comments{/tr}
					{/if}
				{/if}
			</a>
		{/if}
	{/if}
</div>
