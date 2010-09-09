{* $Id$ *}
<div class="postfooter">
	<div class="actions">
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

		<a class="link" href="{$post_info.postId|sefurl:blogpost}">{icon _id='page_link' alt="{tr}Permalink{/tr}"}</a>
		<a href='tiki-print_blog_post.php?postId={$post_info.postId}'>{icon _id='printer' alt="{tr}Print{/tr}"}</a>
		{if $prefs.feature_blog_sharethis eq "y"}
			{capture name=shared_title}{tr}Share This{/tr}{/capture}
			{capture name=shared_link_title}{tr}ShareThis via AIM, social bookmarking and networking sites, etc.{/tr}{/capture}
			{wiki}{literal}<script language="javascript" type="text/javascript">
				//Create your sharelet with desired properties and set button element to false
				var object{/literal}{$postId}{literal} = SHARETHIS.addEntry({
					title:'{/literal}{$smarty.capture.shared_title|replace:'\'':'\\\''}{literal}'
				},
				{button:false});
				//Output your customized button
				document.write('<span id="share{/literal}{$postId}{literal}"><a title="{/literal}{$smarty.capture.shared_link_title|replace:'\'':'\\\''}{literal}" href="javascript:void(0);"><img src="http://w.sharethis.com/images/share-icon-16x16.png?CXNID=1000014.0NXC" /></a></span>');
				//Tie customized button to ShareThis button functionality.
				var element{/literal}{$postId}{literal} = document.getElementById("share{/literal}{$postId}{literal}");
				object{/literal}{$postId}{literal}.attachButton(element{/literal}{$postId}{literal});
			</script>{/literal}{/wiki}
		{/if}
	</div>
	{if $blog_post_context eq 'view_blog'}
		<div class="status">
			{if $allow_comments eq 'y' and $prefs.feature_blogposts_comments eq 'y' && $tiki_p_read_comments eq 'y'}
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
		</div>
	{elseif $blog_post_context eq 'view_blog_post' }
		<div class="postfooter-nav">
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
</div>
