{* $Id$ *}
<div class="postfooter">
	<div class="actions">
		<a href='tiki-print_blog_post.php?postId={$postId}'>{icon _id='printer' alt='{tr}Print{/tr}'}</a>
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
	<div class="status">
		<a class="link" href="{$postId|sefurl:blogpost}">{tr}Permalink{/tr}</a>
		{if $allow_comments eq 'y' and $prefs.feature_blogposts_comments eq 'y' && $tiki_p_read_comments eq 'y'}
			{$listpages[ix].comments} {tr}comments{/tr}
 			[<a class="link" href="tiki-view_blog_post.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;postId={$listpages[ix].postId}">{tr}View Comments{/tr}</a>]
		{/if}
	</div>
</div>
{if $prefs.feature_blogposts_comments == 'y'
	&& ($blog_data.allow_comments == 'y' or $blog_data.allow_comments == 'c')
	&& (($tiki_p_read_comments == 'y'
	&& $comments_cant != 0)
	|| $tiki_p_post_comments == 'y'
	|| $tiki_p_edit_comments == 'y')}
	{include file='comments.tpl'}
{/if}
