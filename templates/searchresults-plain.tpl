<ul class="searchresults">
	{foreach item=result from=$results}
	<li>
		<strong>
		{object_link type=$result.object_type id=$result.object_id title=$result.title}

		{if $prefs.feature_search_show_object_type eq 'y'}
			(<span class="objecttype">{$result.object_type|escape}</span>)
		{/if}

		{if $prefs.feature_search_show_pertinence eq 'y' && !empty($result.relevance)}
			<span class="itemrelevance">({tr}Relevance:{/tr} {$result.relevance})</span>
		{/if}
		</strong>

		<blockquote>
			<p>{$result.highlight}</p>

			{if $prefs.feature_search_show_last_modification eq 'y'}
				<div class="searchdate small">{tr}Last modification{/tr}: {$result.modification_date|tiki_long_datetime}</div>
			{/if}
		</blockquote>
	</li>
	{foreachelse}
		<li>{tr}No pages matched the search criteria{/tr}</li>
	{/foreach}
</ul>
{pagination_links cant=$count step=$maxRecords offset=$offset}{/pagination_links}
