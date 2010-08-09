{* $Id$ *}

<div class="postbody">
	<div class="clearfix postbody-title">
		{if $blog_data.use_title eq 'y'}
			<div class="title">
				<h2>{$post_info.title|escape}</h2>
			</div>
		{/if}

		{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
			{if $tags.data|@count >0}
				<div class="freetaglist">
					 {tr}Tags:{/tr}&nbsp;
					{foreach from=$tags.data item=tag}
						{capture name=tagurl}{if (strstr($tag.tag, ' '))}"{$tag.tag}"{else}{$tag.tag}{/if}{/capture}
						{if isset($preview) and $preview eq 'y'}
							<a class="freetag" href="#">{$tag.tag}</a>
						{else}
							<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$tag.tag}</a>
						{/if}
					{/foreach}
				</div>
			{/if}
		{/if}
	</div>

	<div class="author_info">
		{if $blog_data.use_author eq 'y' || $blog_data.add_date eq 'y'}
			{tr}Published {/tr}
		{/if}
		
		{if $blog_data.use_author eq 'y'}
			{tr}by{/tr} {$post_info.user|userlink} 
		{/if}
		
		{if $blog_data.add_date eq 'y'}
			 {tr}on{/tr} {$post_info.created|tiki_short_date}
		{/if}
		
		{if $blog_data.show_avatar eq 'y'}
			{$post_info.avatar}
		{/if}
	</div>

	<div id="post_data">
		{$parsed_data}
	</div>

	{if $pages > 1}
		<div align="center">
			<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$first_page}">{icon _id='resultset_first' alt='{tr}First page{/tr}'}</a>
			<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$prev_page}">{icon _id='resultset_previous' alt='{tr}Previous page{/tr}'}</a>
			<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>
			<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$next_page}">{icon _id='resultset_next' alt='{tr}Next page{/tr}'}</a>
			<a href="tiki-view_blog_post.php?blogId={$smarty.request.blogId}&amp;postId={$smarty.request.postId}&amp;page={$last_page}">{icon _id='resultset_last' alt='{tr}Last page{/tr}'}</a>
		</div>
	{/if}

	{capture name='copyright_section'}
		{include file='show_copyright.tpl'}
	{/capture}
	
	{* When copyright section is not empty show it *}
	{if $smarty.capture.copyright_section neq ''}
		<p class="editdate">
			{$smarty.capture.copyright_section}
		</p>
	{/if}
</div>
