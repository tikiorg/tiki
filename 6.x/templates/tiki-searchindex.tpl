{* $Id$ *}

<div class="nohighlight">
	{if $searchStyle neq "menu" && $prefs.feature_search_show_object_filter eq 'y'}
		{title admpage="search" help="Search"}{tr}Search{/tr}{/title}
		<div class="navbar">
			{tr}Search in{/tr}:
			{foreach item=name key=k from=$where_list}
				{button _auto_args='where,highlight' href="tiki-searchindex.php?where=$k"  _selected="'$where'=='$k'" _selected_class="highlight" _text="$name"}
			{/foreach}
		</div>
	{/if}

	{if $prefs.feature_search_show_search_box eq 'y'}
		<form action="tiki-searchindex.php" method="get" id="search-form" class="findtable">
			<label class="findtitle">
				{tr}Find{/tr} <input id="highlight{$iSearch}" name="highlight" size="14" type="text" accesskey="s" value="{$words}" />
			</label>
			{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y' and $prefs.search_autocomplete eq 'y'}
				{jq}
					$("#highlight{{$iSearch}}").tiki("autocomplete", "pagename");
				{/jq}
			{/if}			
			{if $prefs.feature_search_show_object_filter eq 'y'}
				{if $searchStyle eq "menu" }
					<span class='searchMenu'>
						{tr}in{/tr}
						<select name="where">
							{if empty($where_list)} {* Required when file included outside tiki-searchindex.php. eg. error.rpl *}
								<option value="pages">{tr}Entire Site{/tr}</option>
								{if $prefs.feature_wiki eq 'y'}
									<option value="wikis">{tr}Wiki Pages{/tr}</option>
								{/if}
								{if $prefs.feature_galleries eq 'y'}
									<option value="galleries">{tr}Galleries{/tr}</option>
									<option value="images">{tr}Images{/tr}</option>
								{/if}
								{if $prefs.feature_file_galleries eq 'y'}
									<option value="files">{tr}Files{/tr}</option>
								{/if}
								{if $prefs.feature_forums eq 'y'}
									<option value="forums">{tr}Forums{/tr}</option>
								{/if}
								{if $prefs.feature_faqs eq 'y'}
									<option value="faqs">{tr}Faqs{/tr}</option>
								{/if}
								{if $prefs.feature_blogs eq 'y'}
									<option value="blogs">{tr}Blogs{/tr}</option>
								{/if}
								{if $prefs.feature_directory eq 'y'}
									<option value="directory">{tr}Directory{/tr}</option>
								{/if}
								{if $prefs.feature_articles eq 'y'}
									<option value="articles">{tr}Articles{/tr}</option>
								{/if}
								{if $prefs.feature_trackers eq 'y'}
									<option value="trackers">{tr}Trackers{/tr}</option>
								{/if}
							{else}
								{foreach item=name key=k from=$where_list}
									<option value="{$k}">{$name}</option>
								{/foreach}
							{/if}
						</select>
					</span>
				{else}
					<input type="hidden" name="where" value="{$where|escape}" />
					{if $forumId}<input type="hidden" name="forumId" value="{$forumId}" />{/if}
				{/if}
			{elseif !empty($where)}
				<input type="hidden" name="where" value="{$where|escape}" />
				{if $forumId}<input type="hidden" name="forumId" value="{$forumId|escape}" />{/if}	
			{/if}
			<label class="findsubmit">
				<input type="submit" name="search" value="{tr}Go{/tr}"/>
			</label>
			{if !$searchNoResults}
				{button _auto_args='highlight' href="tiki-searchindex.php?highlight=" _text="{tr}Clear Filter{/tr}"}
			{/if}
		</form>
	{/if}
</div><!--nohighlight-->
	{* do not change the comment above, since smarty 'highlight' outputfilter is hardcoded to find exactly this... instead you may experience white pages as results *}

{if $words neq '' and !$searchNoResults}
	<div class="nohighlight simplebox">
		{tr}Found{/tr} "{$words}" {tr}in{/tr} {$cant} {tr}{$where}{/tr}
	</div><!--nohighlight-->
	<ul class="searchresults">
		{section name=search loop=$results}
		<li>
			{if $prefs.feature_search_show_object_type eq 'y'}
				<span class="objecttype">{tr}{$results[search].location}{/tr}:</span>
			{/if}
			<a href="{$results[search].href}&amp;highlight={$words}{$results[search].anchor}" class="objectname">{$results[search].pageName|strip_tags|escape}</a> 
			{if $prefs.feature_search_show_visit_count eq 'y'}
				<span class="itemhits">({tr}Hits{/tr}: {$results[search].hits})</span>
			{/if}

			{if $prefs.feature_search_show_pertinence eq 'y' && $prefs.feature_search_fulltext eq 'y'}
				<span class="itemrelevance">
					{if $results[search].relevance <= 0}
						({tr}Simple search{/tr})
					{else}
						({tr}Relevance{/tr}: {$results[search].relevance})
					{/if}
				</span>
			{/if}

			<div class="searchdesc">{$results[search].data|strip_tags|truncate:250:'...'}</div>
			{if $prefs.feature_search_show_last_modification eq 'y'}
				<div class="searchdate">{tr}Last modification{/tr}: {$results[search].lastModif|tiki_long_datetime}</div>
			{/if}
		</li>
		{sectionelse}
			{tr}No pages matched the search criteria{/tr}
		{/section}
	</ul>
	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{/if}
