<div class="author_actions pull-right btn-group">
	{if $blog_post_context ne 'print'}
		{if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
			{icon name="edit" href="tiki-blog_post.php?blogId={$post_info.blogId}&amp;postId={$post_info.postId}" title="{tr}Edit{/tr}"}
			{icon name="remove" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;remove={$post_info.postId}" title="{tr}Remove{/tr}"}
		{/if}
		{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
			{if $blog_post_context eq 'view_blog'}
				{icon name="notepad" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;savenotepad={$post_info.postId}" title="{tr}Save to notepad{/tr}" }
			{else}
				{icon name="notepad" href="tiki-view_blog_post.php?postId={$smarty.request.postId}&amp;savenotepad=1" title="{tr}Save to notepad{/tr}" }
			{/if}
		{/if}
	{/if}
</div>
