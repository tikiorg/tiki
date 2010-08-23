{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="blog_last_comments" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$comments nonums=$nonums}
	{section name=ix loop=$comments}
		<li>
			{if isset($comments[ix].anonymous_name)}
				{if !empty($comments[ix].website)}
					<a class="linkmodule" href="{$comments[ix].website}">
				{/if}
				{$comments[ix].anonymous_name}
				{if !empty($comments[ix].website)}
					</a> 
				{/if}
			{else}
				{$comments[ix].userName|userlink} 
			{/if}
			{tr}on{/tr} <a class="linkmodule" href="tiki-view_blog_post.php?postId={$comments[ix].postId}&amp;comzone=show#threadId{$comments[ix].threadId}" title="{tr} Published on{/tr} {$comments[ix].commentData|tiki_short_date}">{$comments[ix].title|escape}{if $comments[ix].priv eq 'y'} ({tr}private{/tr}){/if}</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
