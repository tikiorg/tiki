{* $Id$ *}
<div class="postfooter">
	<div class="actions">
		{if ($ownsblog eq 'y') or ($user and $listpages[ix].user eq $user) or $tiki_p_blog_admin eq 'y'}
			<a class="blogt" href="tiki-blog_post.php?blogId={$listpages[ix].blogId}&amp;postId={$listpages[ix].postId}">{icon _id='page_edit'}</a>&nbsp;<a class="blogt" href="tiki-view_blog.php?blogId={$blogId}&amp;remove={$listpages[ix].postId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
		{/if}
		{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
			<a title="{tr}Save to notepad{/tr}" href="tiki-view_blog.php?blogId={$blogId}&amp;savenotepad={$listpages[ix].postId}">{icon _id='disk' alt='{tr}Save to notepad{/tr}'}</a>
		{/if}

		<a class="link" href="{$listpages[ix].postId|sefurl:blogpost}">{icon _id='page_link' alt='{tr}Permalink{/tr}'}</a>
		<a href='tiki-print_blog_post.php?postId={$listpages[ix].postId}'>{icon _id='printer' alt='{tr}Print{/tr}'}</a>
		{if $prefs.feature_blog_sharethis eq "y"}
			{capture name=shared_title}{tr}Share This{/tr}{/capture}
			{capture name=shared_link_title}{tr}ShareThis via AIM, social bookmarking and networking sites, etc.{/tr}{/capture}
			{wiki}{literal}<script language="javascript" type="text/javascript">
				//Create your sharelet with desired properties and set button element to false
				var object{/literal}{$listpages[ix].postId}{literal} = SHARETHIS.addEntry({
						title:'{/literal}{$smarty.capture.shared_title|replace:'\'':'\\\''}{literal}',
						url:'{/literal}http://{$hostname}{$smarty.server.SERVER_NAME}{$smarty.server.SCRIPT_NAME|replace:'tiki-view_blog.php':'tiki-view_blog_post.php'}?postId={$listpages[ix].postId}{literal}'
					},
					{button:false});
				//Output your customized button
				document.write('<span id="share{/literal}{$listpages[ix].postId}{literal}"><a title="{/literal}{$smarty.capture.shared_link_title|replace:'\'':'\\\''}{literal}" href="javascript:void(0);"><img src="http://w.sharethis.com/images/share-icon-16x16.png?CXNID=1000014.0NXC" /></a></span>');
				//Tie customized button to ShareThis button functionality.
				var element{/literal}{$listpages[ix].postId}{literal} = document.getElementById("share{/literal}{$listpages[ix].postId}{literal}");
				object{/literal}{$listpages[ix].postId}{literal}.attachButton(element{/literal}{$listpages[ix].postId}{literal});
			</script>{/literal}{/wiki}
		{/if}
	</div>

	<div class="status">
		{if $allow_comments eq 'y' and $prefs.feature_blogposts_comments eq 'y' && $tiki_p_read_comments eq 'y'}
			<a class="link linkcomments" href="tiki-view_blog_post.php?find={$find|escape:url}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;postId={$listpages[ix].postId}#comments">
			{if $listpages[ix].comments == 0 && $tiki_p_post_comments eq 'y'}
				{tr}Leave a comment{/tr}
			{else}
				{$listpages[ix].comments}
				{if $listpages[ix].comments == 1}
					{tr}comment{/tr}
				{else}
					{tr}comments{/tr}
				{/if}
			{/if}
			</a>
		{/if}
	</div>
</div> <!--postfooter-->
