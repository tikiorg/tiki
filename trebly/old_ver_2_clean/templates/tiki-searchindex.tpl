{* $Id$ *}

<div class="nohighlight">
	{if $searchStyle neq "menu" && $prefs.feature_search_show_object_filter eq 'y'}
		{title admpage="search" help="Search"}{tr}Search{/tr}{/title}
		<div class="navbar">
			{tr}Search in:{/tr}
			{foreach item=name key=k from=$where_list}
				{button _auto_args='where,highlight' href="tiki-searchindex.php?where=$k"  _selected="'$where'=='$k'" _selected_class="highlight" _text="$name"}
			{/foreach}
		</div>
	{/if}

	{if $prefs.feature_search_show_search_box eq 'y'}
		{filter action="tiki-searchindex.php"}{/filter}
	{/if}
</div><!--nohighlight-->
	{* do not change the comment above, since smarty 'highlight' outputfilter is hardcoded to find exactly this... instead you may experience white pages as results *}

{$results}

