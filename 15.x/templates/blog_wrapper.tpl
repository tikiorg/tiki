{* $Id$ *}
<div class="postbody clearfix">
	<a id="postId{$post_info.postId}"></a>
	{include file='blog_post_postbody_title.tpl'}
	{include file='blog_post_postbody_content.tpl'}
	{if $blog_post_context ne 'excerpt'}
		{if $blog_post_context ne 'print'}
			<footer class="postfooter panel-footer clearfix">
				{include file='blog_post_actions.tpl'}
				{* Copyright display is being turned off if being called through the "BLOG" plugin with "simple" mode turned off and a max character count supplied (preview mode). If in preview mode end user most likely is wanting to conserve space with a smaller display of information *}
				{if $blog_post_context neq 'plugin_preview'}
					{capture name='copyright_section'}
						{include file='show_copyright.tpl'}
					{/capture}
					{* When copyright section is not empty show it *}
					{if $smarty.capture.copyright_section neq ''}
						<div class="help-block">
							{$smarty.capture.copyright_section}
						</div>
					{/if}
				{/if}
				{include file='blog_post_status.tpl'}
                {include file='blog_post_navigation.tpl'}
			</footer>
		{else}
			{* Show copyright information in print view *}
			{if $blog_post_context neq 'plugin_preview'}
				{capture name='copyright_section'}
					{include file='show_copyright.tpl'}
				{/capture}
				{* When copyright section is not empty show it *}
				{if $smarty.capture.copyright_section neq ''}
					<div class="help-block">
						{$smarty.capture.copyright_section}
					</div>
				{/if}
			{/if}
		{/if}
	{/if}
</div> <!-- postbody -->
{* {include file='blog_post_navigation.tpl'} *}
