{* $Id$ *}
{if $facets|@count}
	<div class="facets" style="width: 25%; float: right;">
		{foreach from=$facets item=facet}
			<h6>{$facet.label|escape}</h6>
			<select multiple data-for="#search-form input[name='filter~{$facet.name|escape}']" data-join="{$facet.operator|escape}">
				{foreach from=$facet.options key=value item=label}
					<option value="{$value|escape}">{$label|escape}</option>
				{/foreach}
			</select>
		{/foreach}
		<p>
			<button>{tr}Filter{/tr}</button>
		</p>
	</div>
	{jq}
		$('.facets select').registerFacet();
		$('.facets button').click(function () {
			$('#search-form').submit();
		});

	{/jq}
{/if}
<div>
<ul class="searchresults">
	{foreach item=result from=$results}
	<li>
		<strong>
		{object_link type=$result.object_type id=$result.object_id title=$result.title url=$result.url}

		{if $prefs.feature_search_show_object_type eq 'y'}
			(<span class="objecttype">{tr}{$result.object_type|escape}{/tr}</span>)
		{/if}

		{if $prefs.feature_search_show_pertinence eq 'y' && !empty($result.relevance)}
			<span class="itemrelevance">({tr}Relevance:{/tr} {$result.relevance|escape})</span>
		{/if}

		{if $prefs.feature_search_show_visit_count eq 'y' and $result.visits neq null}
			<span class="itemhits">({tr}Visits:{/tr} {$result.visits|escape})</span>
		{/if}

		{if !empty($result.parent_object_id)} {tr}in{/tr} {object_link type=$result.parent_object_type id=$result.parent_object_id}{/if}
		</strong>

		<blockquote>
			<p>{$result.highlight}</p>

			{if $prefs.feature_search_show_last_modification eq 'y'}
				<div class="searchdate small">{tr}Last modification:{/tr} {$result.modification_date|tiki_long_datetime}</div>
			{/if}
		</blockquote>
	</li>
	{foreachelse}
		<li>{tr}No pages matched the search criteria{/tr}</li>
	{/foreach}
</ul>
{pagination_links resultset=$results}{/pagination_links}
</div>
