{* $Id$ *}
<div class="margin-bottom-md nohighlight">
	{if $prefs.feature_search_show_search_box eq 'y'}
		{filter action="tiki-searchindex.php" filter=$filter}{/filter}
	{/if}
</div><!--nohighlight-->
	{* do not change the comment above, since smarty 'highlight' outputfilter is hardcoded to find exactly this... instead you may experience white pages as results *}

{if isset($results)}
	{$results}
{/if}
