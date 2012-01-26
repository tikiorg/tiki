<div class="author_actions">
	{if $blog_post_context ne 'print'}
		{if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
			<a class="blogt" href="tiki-blog_post.php?blogId={$post_info.blogId}&amp;postId={$post_info.postId}">{icon _id='page_edit'}</a>
			<a class="blogt" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;remove={$post_info.postId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
		{/if}
		{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
			{if $blog_post_context eq 'view_blog'}
				<a title="{tr}Save to notepad{/tr}" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;savenotepad={$post_info.postId}">{icon _id='disk' alt="{tr}Save to notepad{/tr}"}</a>
			{else}
				<a title="{tr}Save to notepad{/tr}" href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;savenotepad=1">{icon _id='disk' alt="{tr}Save to notepad{/tr}"}</a>
			{/if}
		{/if}
	{/if}
</div>
