{strip}
{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="top_visited_blogs" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modTopVisitedBlogs nonums=$nonums}
	{section name=ix loop=$modTopVisitedBlogs}
	<li>
		{if $module_params.showlastpost eq 'y'}
			<a class="linkmodule" href="{$modTopVisitedBlogs[ix].blogId|sefurl:blog}">
				{$modTopVisitedBlogs[ix].postTitle|escape}
			</a>
			<br>{tr}Posted to:{/tr} {$modTopVisitedBlogs[ix].title|escape}
			<div class="description help-block">
				{capture name="parse"}{wiki}{$modTopVisitedBlogs[ix].postData}{/wiki}{/capture}
				{$smarty.capture.parse|strip_tags|truncate:250:'...'|escape}
				<a class="linkmodule more" href="{$modTopVisitedBlogs[ix].blogId|sefurl:blog}">
					{tr}(Read More){/tr}
				</a>
			</div>
		{else}
			<a class="linkmodule" href="{$modTopVisitedBlogs[ix].blogId|sefurl:blog}">
				{$modTopVisitedBlogs[ix].title|escape}
			</a>
		{/if}
	</li>
	{/section}
{/modules_list}
{/tikimodule}
{/strip}
