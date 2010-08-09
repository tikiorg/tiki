{* $Id$ *}
<div class="actions">
	{if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
		<a class="blogt" href="tiki-blog_post.php?blogId={$post_info.blogId}&amp;postId={$post_info.postId}">{icon _id='page_edit'}</a>
		<a class="blogt" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;remove={$post_info.postId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
	{/if}
	{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<a title="{tr}Save to notepad{/tr}" href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;savenotepad=1">{icon _id='disk' alt='{tr}Save to notepad{/tr}'}</a>
	{/if}
</div>
