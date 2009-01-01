<html><body>
<div style="margin:40px;">
<h2>{tr}Viewing blog post{/tr}</h2>
<a class="link" href="tiki-view_blog.php?find={$find}&amp;blogId={$blogId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}">{tr}Return to blog{/tr}</a>
<br /><br />
<div class="posthead">
<table ><tr><td align="left">
<span class="posthead">
{if $blog_data.use_title eq 'y'}
	{$post_info.title}<br />
	<small> {tr}Posted by{/tr} {$post_info.user} on {$post_info.created|tiki_short_datetime}</small>
{else}
	{$post_info.created|tiki_short_datetime}<small> {tr}Posted by{/tr} {$post_info.user}</small>
{/if}
</span>
</td></tr></table>
</div>
<div class="postbody">
{$parsed_data}
<hr/>
<table >
<tr><td>
<small>
{tr}Permalink{/tr}: <a class="link" href="tiki-view_blog_post.php?blogId={$blogId}&amp;postId={$postId}">tiki-view_blog_post.php?blogId={$blogId}&amp;postId={$postId}</a>
{if $allow_comments eq 'y' and $prefs.feature_blogposts_comments eq 'y'}
{$listpages[ix].comments} {tr}comments{/tr}
{/if}
</small>
</td></tr></table>
</div>
</div>

</body>
</html>
