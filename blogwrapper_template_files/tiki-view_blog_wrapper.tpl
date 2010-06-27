{* $Id$ *}
<div class="clearfix postbody">
	{include file='tiki-view_blog_actions.tpl'}
	{include file='tiki-view_blog_author_info.tpl'}
	<a name="postId{$listpages[ix].postId}"></a> {* ?? *}
	{include file='tiki-view_blog_postbody_title.tpl'}
	<div class="postbody-content">
		{$listpages[ix].parsed_data}
		{if $listpages[ix].pages > 1}
			<a class="link more" href="{$listpages[ix].postId|sefurl:blogpost}">
			{tr}More...{/tr} ({$listpages[ix].pages} {tr}pages{/tr})</a>
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
</div> <!-- postbody -->
{include file='tiki-view_blog_footer.tpl'}