{if !empty($container_class)}<div class="{$container_class}">{/if}
{section name=ix loop=$listpages}
	{capture name=href}{strip}
		{if empty($urlparam)}
			{if $useLinktoURL eq 'n' or empty($listpages[ix].linkto)}
				{$listpages[ix].articleId|sefurl:article}
			{else}
				{$listpages[ix].linkto}
			{/if}
		{else}
			{$listpages[ix].articleId|sefurl:article:with_next}{$urlparam}
		{/if}
	{/strip}{/capture}
	<div class="articletitle">
		<span class="newsitem">
			<a href="{$smarty.capture.href}">{$listpages[ix].title|escape}</a>
		</span>
		<br>
	</div>
{/section}
{if !empty($listpages) && (isset($usePagination) and $usePagination ne 'n')}
	{pagination_links cant=$cant step=$maxArticles offset=$offset}{if isset($urlnext)}{$urlnext}{/if}{/pagination_links}
{/if}
{if !empty($container_class)}</div>{/if}
