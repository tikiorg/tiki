{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="blog_last_comments" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
		{section name=ix loop=$comments}
			<li>
				<a class="linkmodule" href="tiki-view_blog_post.php?postId={$comments[ix].postId}&amp;comzone=show#threadId{$comments[ix].threadId}" title="{$comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$comments[ix].userName|escape}{if $moretooltips eq 'y'} {tr}on blogpost{/tr} {$comments[ix].title|escape}{/if}">
					{if $moretooltips ne 'y'}<b>{$comments[ix].title|escape}:</b>{/if}
					{$comments[ix].commentTitle|escape}
					{if $nodate eq 'n'}
						<small class="description">{$comments[ix].commentDate|tiki_short_datetime}</small>
					{/if}
				</a>
			</li>
		{/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}