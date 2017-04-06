{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="blog_last_comments" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$comments nonums=$nonums}
	{section name=ix loop=$comments}
	<li>
		{if $prefs.comments_notitle eq 'y'}
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
			{tr}on{/tr} <a class="linkmodule" href="tiki-view_blog_post.php?postId={$comments[ix].postId}&amp;comzone=show#threadId={$comments[ix].threadId}" title="{tr}Published on{/tr} {$comments[ix].commentData|tiki_short_date:'n'}">{$comments[ix].title|escape}{if $comments[ix].priv eq 'y'} ({tr}private{/tr}){/if}</a>
		{else}
			<a class="linkmodule clearfix" href="tiki-view_blog_post.php?postId={$comments[ix].postId}&amp;comzone=show#threadId={$comments[ix].threadId}" title="{tr}Published on{/tr} {$comments[ix].commentDate|tiki_short_datetime:'':'n'}, {tr}by{/tr} {$comments[ix].userName}{if $moretooltips eq 'y'} {tr}on blogpost{/tr} {$comments[ix].title}{/if}">
				{if $moretooltips ne 'y'}{$comments[ix].title}{if $comments[ix].priv eq 'y'} ({tr}private{/tr}){/if}:{/if}
				{if $comments[ix].commentTitle ne ''}<span class="commentTitle">{$comments[ix].commentTitle}</span>{else}{tr}Untitled{/tr}{/if}
			</a>
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
			{if $module_params.nodate neq 'y'}
				<small class="date">{$comments[ix].commentDate|tiki_short_datetime}</small>
			{/if}
		{/if}
	</li>
	{/section}
{/modules_list}
{/tikimodule}
