<div class="author_actions"{if $prefs.mobile_mode eq "y"} data-role="controlgroup" data-type="horizontal"{/if}> {* mobile *}
	{if $blog_post_context ne 'print'}
		{if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
			<a {if $prefs.mobile_mode eq "y"}data-role="button" data-inline="true" {/if}class="blogt" href="tiki-blog_post.php?blogId={$post_info.blogId}&amp;postId={$post_info.postId}">{icon _id='page_edit'}</a> {* mobile *}
			<a {if $prefs.mobile_mode eq "y"}data-role="button" data-inline="true" {/if}class="blogt" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;remove={$post_info.postId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a> {* mobile *}
		{/if}
		{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
			{if $blog_post_context eq 'view_blog'}
				<a {if $prefs.mobile_mode eq "y"}data-role="button" data-inline="true" {/if}title="{tr}Save to notepad{/tr}" href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;savenotepad={$post_info.postId}">{icon _id='disk' alt="{tr}Save to notepad{/tr}"}</a> {* mobile *}
			{else}
				<a {if $prefs.mobile_mode eq "y"}data-role="button" data-inline="true" {/if}title="{tr}Save to notepad{/tr}" href="tiki-view_blog_post.php?postId={$smarty.request.postId}&amp;savenotepad=1">{icon _id='disk' alt="{tr}Save to notepad{/tr}"}</a> {* mobile *}
			{/if}
		{/if}
	{/if}
</div>
