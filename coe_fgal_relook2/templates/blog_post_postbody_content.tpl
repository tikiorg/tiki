{* $Id$ *}
<div class="postbody-content">
	{$parsed_data}
</div>
<div class="postbody-pagination">
	{if $pages > 1}
		<div align="center">
			<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$first_page}">{icon _id='resultset_first' alt='{tr}First page{/tr}'}</a>
			<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$prev_page}">{icon _id='resultset_previous' alt='{tr}Previous page{/tr}'}</a>
			<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>
			<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$next_page}">{icon _id='resultset_next' alt='{tr}Next page{/tr}'}</a>
			<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$last_page}">{icon _id='resultset_last' alt='{tr}Last page{/tr}'}</a>
		</div>
	{/if}
</div>
{capture name='copyright_section'}
	{include file='show_copyright.tpl'}
{/capture}
{* When copyright section is not empty show it *}
{if $smarty.capture.copyright_section neq ''}
	<div class="editdate">{$smarty.capture.copyright_section}</div>
{/if}
