<div class="actions pull-right btn-group dropup">
	<a class="btn btn-link" data-toggle="dropdown" href="#">
		{icon name="wrench"}
	</a>
	<ul class="dropdown-menu">
		<li class="dropdown-title">
			{tr}Blog post actions{/tr}
		</li>
		<li class="divider"></li>
		<li>
			<a href="tiki-print_blog_post.php?postId={$post_info.postId}">
				{icon name="print" _menu_text='y' _menu_icon='y' alt="{tr}Print{/tr}"}
			</a>
		</li>
		{if $blog_post_context ne 'print'}
			{if ($ownsblog eq 'y') or ($user and $post_info.user eq $user) or $tiki_p_blog_admin eq 'y'}
				<li>
					<a href="tiki-blog_post.php?blogId={$post_info.blogId}&amp;postId={$post_info.postId}">
						{icon name="edit"} {tr}Edit{/tr}
					</a>
				</li>
				<li>
					<a href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;remove={$post_info.postId}">
						{icon name="remove"} {tr}Remove{/tr}
					</a>
				</li>
			{/if}
			{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
				<li>
					{if $blog_post_context eq 'view_blog'}
						<a href="tiki-view_blog.php?blogId={$post_info.blogId}&amp;savenotepad={$post_info.postId}">
							{icon name="notepad"} {tr}Save to notepad{/tr}
						</a>
					{else}
						<a href="tiki-view_blog_post.php?postId={$smarty.request.postId}&amp;savenotepad=1">
							{icon name="notepad"} {tr}Save to notepad{/tr}
						</a>
					{/if}
				</li>
			{/if}
		{/if}
		{if $prefs.feature_blog_sharethis eq "y"}
			<li>
				{literal}
				<script type="text/javascript">
					//Create your sharelet with desired properties and set button element to false
					var object{/literal}{$postId}{literal} = SHARETHIS.addEntry({}, {button:false});
					//Output your customized button
					document.write('<a id="share{/literal}{$postId}{literal}" href="#">{/literal}{icon name="sharethis"}  {tr}ShareThis{/tr}{literal}</a>');
					//Tie customized button to ShareThis button functionality.
					var element{/literal}{$postId}{literal} = document.getElementById("share{/literal}{$postId}{literal}");
					object{/literal}{$postId}{literal}.attachButton(element{/literal}{$postId}{literal});
				</script>
				{/literal}
			</li>
		{/if}
	</ul>
</div>
