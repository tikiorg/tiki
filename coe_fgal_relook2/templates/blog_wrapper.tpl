{* $Id$ *}
<div class="clearfix postbody">
	<a name="postId{$listpages[ix].postId}"></a>
	{include file='blog_postbody_title.tpl'}
	{include file='blog_author_info.tpl'}
	<div class="postbody-content">
		{if $post_list eq 'y' && $use_excerpt eq 'y' && !empty($listpages[ix].excerpt)}
			{$listpages[ix].excerpt}
			<br />
			{self_link _script='tiki-view_blog_post.php' postId=$listpages[ix].postId}{tr}Read more{/tr}{/self_link}
		{else}
			{$listpages[ix].parsed_data}
			{if $listpages[ix].pages > 1}
				<a class="link more" href="{$listpages[ix].postId|sefurl:blogpost}">
				{tr}More...{/tr} ({$listpages[ix].pages} {tr}pages{/tr})</a>
			{/if}
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
	{include file='blog_footer.tpl'}
</div> <!-- postbody -->
