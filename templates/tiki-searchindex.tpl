{* $Id$ *}
{extends 'layout_view.tpl'}

{block name=title}
	{title help="Search" admpage="search"}{tr}Search{/tr}{/title}
{/block}

{block name=content}
<div class="nohighlight">
	{if $prefs.feature_search_show_search_box eq 'y'}
		{filter action="tiki-searchindex.php" filter=$filter}{/filter}
	{/if}
</div><!--nohighlight-->
	{* do not change the comment above, since smarty 'highlight' outputfilter is hardcoded to find exactly this... instead you may experience white pages as results *}

{if isset($results)}
	{$results}
{/if}
{/block}
