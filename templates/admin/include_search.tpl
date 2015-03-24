{* $Id$ *}

{if $prefs.feature_search_stats eq 'y'}
	{remarksbox type="tip" title="{tr}Tip{/tr}"}
		{tr}Search stats{/tr} {tr}can be seen on page{/tr} <a class='rbox-link' target='tikihelp' href='tiki-search_stats.php'>{tr}Search stats{/tr}</a> {tr}in Admin menu{/tr}
	{/remarksbox}
{/if}

{if $prefs.feature_file_galleries eq 'y'}
	{remarksbox type="tip" title="{tr}Tip{/tr}"}
		{tr}Also see the Search Indexing tab here:{/tr} <a class='rbox-link' target='tikihelp' href='tiki-admin.php?page=fgal'>{tr}File Gallery admin panel{/tr}</a>
	{/remarksbox}
{/if}


<form action="tiki-admin.php?page=search" method="post">
	<input type="hidden" name="searchprefs" />
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" class="btn btn-default" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name=admin_search}
		{tab name="{tr}General Settings{/tr}"}
		
			<fieldset>
				<legend>
					{tr}Basic Search{/tr} {help url="Search"}
				</legend>
				{preference name=feature_search_fulltext}
				<div class="adminoptionboxchild" id="feature_search_fulltext_childcontainer">				
					{preference name=feature_referer_highlight}

					{preference name=feature_search_show_forbidden_obj}
					{preference name=feature_search_show_forbidden_cat}
				</div>
			</fieldset>
		
			<fieldset>
				<legend>
					{tr}Advanced Search{/tr}
				</legend>

				{if $prefs.unified_last_rebuild}
					{remarksbox type=info title="{tr}Last rebuild{/tr}"}
						{tr _0=$prefs.unified_last_rebuild|tiki_long_datetime}Your index was last fully rebuilt on %0.{/tr}
					{/remarksbox}
				{/if}

				{preference name=feature_search visible="always"}
				<div class="adminoptionboxchild" id="feature_search_childcontainer">
					{remarksbox type=tip title="{tr}About the Unified Index{/tr}"}
						{tr}The Unified Index provides many underlying features for Tiki, including object selectors for translations amongst other things.{/tr}
						{tr}Disabling this will cause some parts of Tiki to be unavailable.{/tr}<br>
						<a href="http://doc.tiki.org/Unified+Index">{tr}Find out more about it here.{/tr}</a>
					{/remarksbox}

					{preference name=feature_search_stats}
					{preference name=user_in_search_result}
					{preference name="unified_incremental_update"}

					{preference name="allocate_memory_unified_rebuild"}
					{preference name="allocate_time_unified_rebuild"}
					
					{preference name="unified_engine"}
					{remarksbox type=tip title="{tr}About Unified Search Engines{/tr}"}
						<b>{tr}MySQL Full Text Search{/tr}: </b></br>
						{tr}Advantages{/tr}: {tr}Fast performance. Works out of the box with Tiki and even on most basic server setups{/tr}.</br>
						{tr}Disadvantages{/tr}: {tr}Many common words (such as "first", "second", and "third" are not searchable unless MySQL configuration is modified). Only the first 65,535 characters (about 8000 words) of long pieces of content are searchable{/tr}(See this <a class='rbox-link' href='http://dev.mysql.com/doc/refman/5.7/en/fulltext-stopwords.html'>{tr}link{/tr}</a> {tr} for full list) {/tr}</br>
						<b>{tr}Lucene (PHP implementation){/tr}: </b></br>
						{tr}Advantages{/tr}: {tr}Overcomes limitations of MySQL search mentioned above. Comes built in with Tiki{/tr}.</br>
						{tr}Disadvantages{/tr}: {tr}Slower performance. May not work well with the most basic server setups and because the index is stored on disk it is more prone to file permissions problems and other server configuration issues{/tr}.</br>
						<b>{tr}ElasticSearch{/tr}: </b></br>
						{tr}Advantages{/tr}: {tr}Most advanced, fast and scalable search engine. Enables some very advanced/new features of Tiki{/tr}.</br>
						{tr}Disadvantages{/tr}: {tr}Needs to be separately installed from Tiki and requires more configuration{/tr} (See this <a class='rbox-link' href='http://doc.tiki.org/ElasticSearch'>{tr}link{/tr}</a> {tr}for more information) {/tr}</br>
					{/remarksbox}
					{if ! empty($engine_info)}
						<div class="adminoptionboxchild">
							<ul>
								{foreach from=$engine_info key=property item=value}
									<li><strong>{$property|escape}:</strong> {$value|escape}</li>
								{/foreach}
							</ul>
						</div>
					{/if}
					<div class="adminoptionboxchild unified_engine_childcontainer lucene">
						{preference name="unified_lucene_highlight"}
						{preference name=unified_parse_results}
						{preference name="unified_lucene_default_operator"}

						<fieldset>
							<legend>{tr}Search Engine Settings{/tr}</legend>
							{preference name="unified_lucene_location"}
							{preference name="unified_lucene_max_result"}
							{preference name="unified_lucene_max_resultset_limit"}
							{preference name="unified_lucene_terms_limit"}
							{preference name="unified_lucene_max_buffered_docs"}
							{preference name="unified_lucene_max_merge_docs"}
							{preference name="unified_lucene_merge_factor"}
						</fieldset>
					</div>

					<div class="adminoptionboxchild unified_engine_childcontainer elastic">
						{preference name="unified_elastic_url"}
						{preference name="unified_elastic_index_prefix"}
						{preference name="unified_elastic_index_current"}
					</div>

					{preference name=unified_excluded_categories}
					{preference name=unified_excluded_plugins}

					{preference name=unified_exclude_all_plugins}
					<div class="adminoptionboxchild" id="unified_exclude_all_plugins_childcontainer">
						{preference name=unified_included_plugins}
					</div>

					{preference name=unified_forum_deepindexing}

					{preference name=unified_tokenize_version_numbers}

					{preference name=unified_field_weight}
					{preference name=unified_default_content}
					{preference name=unified_user_cache}

					{preference name=unified_cached_formatters}

					{preference name=unified_trackerfield_keys}
					{preference name=unified_add_to_categ_search}

					<h4>{tr}Index maintenance{/tr}</h4>
					<ul>
						<li><a href="tiki-admin.php?page=search&amp;optimize=now">{tr}Optimize{/tr}</a> {tr}From the command line:{/tr} <kbd>php console.php{if not empty($tikidomain)} --site={$tikidomain|replace:'/':''}{/if} index:optimize</kbd></li>
						<li>
							<a href="tiki-admin.php?page=search&amp;rebuild=now" id="rebuild-link">{tr}Rebuild Index{/tr}</a> {tr}From the command line:{/tr} <kbd> php console.php{if not empty($tikidomain)} --site={$tikidomain|replace:'/':''}{/if} index:rebuild</kbd><br>
							<label for="log-rebuild">{tr}Log rebuild?{/tr}</label>
							<input type="checkbox" id="log-rebuild" />
							<span class="description">{tr}Log file is saved as temp/Search_Indexer.log (or temp/Search_Indexer_console.log if indexed from the command line){/tr}</span> <br> {tr}From the command line:{/tr} <kbd>php console.php{if not empty($tikidomain)} --site={$tikidomain|replace:'/':''}{/if} index:rebuild --log</kbd><br>
							{jq}
$("#log-rebuild").click(function(){
	if ($(this).prop("checked")) {
		$("#rebuild-link").attr("href", $("#rebuild-link").attr("href") + "&loggit");
	} else {
		$("#rebuild-link").attr("href", $("#rebuild-link").attr("href").replace("&loggit",""));
	}
});
							{/jq}
							{if !empty($stat)}
								{remarksbox type='feedback' title="{tr}Indexation{/tr}"}
									<ul>
										{foreach from=$stat key=what item=nb}
											<li>{$what|escape}: {$nb|escape}</li>
										{/foreach}
									</ul>
								{/remarksbox}
							{/if}
						</li>
					</ul>
					{remarksbox type="tip" title="{tr}Indexing Problems?{/tr}"}
					<p>{tr}If the indexing does not complete, check the log file to see where it ended. Perhaps there could be a problem with the last content it was trying to index. Settings tips: excluding plugins from the indexing usually helps, and it might be necessary to increase the time limit above for large sites. You can also exclude items from being indexed by categorizing them in excluded categories.{/tr}</p>
					<p>{tr}Last line of log file (web):{/tr} {$lastLogItemWeb|escape}<br />
					{tr}Last line of log file (console):{/tr} {$lastLogItemConsole|escape}</p>
					{/remarksbox}
					<h5>{tr}Queue size:{/tr} {$queue_count}</h5>
					{if $queue_count > 0}
						{tr}Process:{/tr}
						<ul>
							{if $queue_count > 10}
								<li><a  href="tiki-admin.php?page=search&amp;process=10">10</a></li>
							{/if}
							{if $queue_count > 20}
								<li><a  href="tiki-admin.php?page=search&amp;process=20">20</a></li>
							{/if}
							{if $queue_count > 0 and !empty($smarty.request.process) and $smarty.request.process eq 'all' and $prefs.javascript_enabled eq "y"}
								{jq} setTimeout(function() { history.go(0); }, 1000); {/jq}
								<li><strong><a  href="tiki-admin.php?page=search&amp;process=">{tr}Stop{/tr}</a></strong></li>
							{else}
								<li><em><a  href="tiki-admin.php?page=search&amp;process=all">{tr}All{/tr}</a></em> <br><span class="description">{tr}Uses JavaScript to reload this page until queue is processed{/tr}</span></li>
							{/if}
						</ul>
					{/if}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Features{/tr}</legend>
				{preference name=search_autocomplete}
			</fieldset>

			<fieldset>
				<legend>{tr}Forum searches{/tr}</legend>
				{preference name=feature_forums_name_search}
				{preference name=feature_forums_search}
				{preference name=feature_forum_content_search}
				<div class="adminoptionboxchild" id="feature_forum_content_search_childcontainer">
					{preference name=feature_forum_local_tiki_search}
					{preference name=feature_forum_local_search}
				</div>
			</fieldset>

		{/tab}

		{tab name="{tr}Search Results{/tr}"}
			{preference name=search_use_facets}
			{preference name=category_custom_facets}
			
			<fieldset>
				<legend>{tr}Select the items to display on the search results page:{/tr}</legend>
				{preference name=search_default_interface_language}
				{preference name=search_default_where}
				{preference name=search_show_category_filter}
				{preference name=search_show_tag_filter}
				{preference name=feature_search_show_object_filter}
				{preference name=search_show_sort_order}
				{preference name=feature_search_show_search_box}
			</fieldset>
			<fieldset>
				<legend>{tr}Select the information to display for each result:{/tr}</legend>
				{preference name=feature_search_show_visit_count}
				{preference name=feature_search_show_pertinence}
				{preference name=feature_search_show_object_type}
				{preference name=feature_search_show_last_modification}
				{preference name=search_parsed_snippet}
			</fieldset>
		{/tab}

	{tab name="{tr}Tools{/tr}"}
		<a href="tiki-report_string_in_db.php">{tr}Report all occurences of a string in any table{/tr}</a><br>
	{/tab}

	{/tabset}
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" class="btn btn-default" value="{tr}Change preferences{/tr}" />
	</div>
</form>
