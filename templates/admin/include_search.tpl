{*$Id$ *}

{if$prefs.feature_search_stats eq 'y'}
	{remarksboxtype="tip" title="{tr}Tip{/tr}"}
		{tr}Searchstats{/tr} {tr}can be seen on page{/tr} <a class='alert-link' target='tikihelp' href='tiki-search_stats.php'>{tr}Search stats{/tr}</a> {tr}in Admin menu{/tr}
	{/remarksbox}
{/if}

{if$prefs.feature_file_galleries eq 'y'}
	{remarksboxtype="tip" title="{tr}Tip{/tr}"}
		{tr}Alsosee the Search Indexing tab:{/tr} <a class='alert-link' target='tikihelp' href='tiki-admin.php?page=fgal'>{tr}File Gallery admin panel{/tr}</a>
	{/remarksbox}
{/if}


<formaction="tiki-admin.php?page=search" method="post">
	<inputtype="hidden" name="searchprefs" />

	{if$prefs.feature_search eq 'y'}
		{remarksboxtype=tip title="{tr}Index maintenance{/tr}"}
			{if$prefs.unified_last_rebuild}
				<p>{tr_0=$prefs.unified_last_rebuild|tiki_long_datetime}Your index was last fully rebuilt on %0.{/tr}</p>
			{/if}

			<p>
				<aclass="btn btn-primary" href="tiki-admin.php?page=search&amp;rebuild=now" id="rebuild-link">{tr}Rebuild Index{/tr}</a>
				<aclass="btn btn-default" href="tiki-admin.php?page=search&amp;rebuild=now&amp;loggit" id="rebuild-link">{tr}Rebuild Index with Log{/tr}</a>
				<aclass="btn btn-default" href="tiki-admin.php?page=search&amp;optimize=now">{tr}Optimize{/tr}</a>
			</p>
			<p>{tr}Logfile is saved as temp/Search_Indexer.log{/tr}</p>

			<h5>{tr}Queuesize:{/tr} {$queue_count}</h5>
			{if$queue_count > 0}
				{foreach[10, 20, 50, 100] as $count}
					{if$queue_count > $count}
						<aclass="btn btn-default" href="tiki-admin.php?page=search&amp;process={$count|escape}">{tr _0=$count}Process %0{/tr}</a>
					{/if}
				{/foreach}
				{if$queue_count > 0 and !empty($smarty.request.process) and $smarty.request.process eq 'all' and $prefs.javascript_enabled eq "y"}
					{jq}setTimeout(function() { history.go(0); }, 1000); {/jq}
					<aclass="btn btn-warning" href="tiki-admin.php?page=search">{tr}Stop{/tr}</a>
				{else}
					<aclass="btn-warning" href="tiki-admin.php?page=search&amp;process=all">{tr}All{/tr}</a>
					<br><spanclass="description">{tr}Uses JavaScript to reload this page until queue is processed{/tr}</span></li>
				{/if}
			{/if}

		{/remarksbox}

		{if!empty($stat)}
			{remarksboxtype='feedback' title="{tr}Indexation{/tr}"}
				<ul>
					{foreachfrom=$stat key=what item=nb}
						<li>{$what|escape}:{$nb|escape}</li>
					{/foreach}
				</ul>
			{/remarksbox}
		{else}
			{*If the indexing succeeded, there are clearly no problems, free up some screen space *}
			{remarksboxtype=tip title="{tr}Indexing Problems?{/tr}"}
				<p>{tr}Ifthe indexing does not complete, check the log file to see where it ended.{/tr}</p>
				<p>{tr}Lastline of log file (web):{/tr} <strong>{$lastLogItemWeb|escape}</strong></p>
				<p>{tr}Lastline of log file (console):{/tr} <strong>{$lastLogItemConsole|escape}</strong></p>

				<p>Commonfailures include:</p>
				<ul>
					<li><strong>{tr}Notenough memory.{/tr}</strong> Larger sites require more memory to re-index.</li>
					<li><strong>{tr}Timelimit too short.{/tr}</strong> It may be required to run the rebuild through the command line.</li>
					<li><strong>{tr}Highresource usage.{/tr}</strong> Some plugins in your pages may cause excessive load. Blacklisting some plugins during indexing can help.</li>
				</ul>
			{/remarksbox}
		{/if}
		{remarksboxtype=tip title="{tr}Command Line Utilities{/tr}"}
			<kbd>phpconsole.php index:optimize</kbd><br>
			<kbd>phpconsole.php index:rebuild</kbd><br>
			<kbd>phpconsole.php index:rebuild --log</kbd><br>
			<p>{tr}Logfile is saved as temp/Search_Indexer_console.log{/tr}</p>
		{/remarksbox}

	{/if}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<divclass="pull-right">
				<inputtype="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
	{tabsetname=admin_search}
		{tabname="{tr}General Settings{/tr}"}
			<h2>{tr}GeneralSettings{/tr}</h2>

			<fieldset>
				<legend>
					{tr}AdvancedSearch{/tr}
				</legend>

				{preferencename=feature_search visible="always"}
				<divclass="adminoptionboxchild" id="feature_search_childcontainer">
					{remarksboxtype=tip title="{tr}About the Unified Index{/tr}"}
						{tr}TheUnified Index provides many underlying features for Tiki, including object selectors for translations amongst other things.{/tr}
						{tr}Disablingthis will cause some parts of Tiki to be unavailable.{/tr}<br>
						<ahref="http://doc.tiki.org/Unified+Index">{tr}Find out more about it here.{/tr}</a>
					{/remarksbox}

					{preferencename=feature_search_stats}
					{preferencename=user_in_search_result}
					{preferencename="unified_incremental_update"}

					{preferencename="allocate_memory_unified_rebuild"}
					{preferencename="allocate_time_unified_rebuild"}

					{preferencename="unified_engine"}

					{remarksboxtype=tip title="{tr}About Unified Search Engines{/tr}"}
						<b>{tr}MySQLFull Text Search{/tr}: </b></br>
						{tr}Advantages{/tr}:{tr}Fast performance. Works out of the box with Tiki and even on most basic server setups{/tr}.</br>
						{tr}Disadvantages{/tr}:{tr}Many common words (such as "first", "second", and "third" are not searchable unless MySQL configuration is modified). Only the first 65,535 characters (about 8000 words) of long pieces of content are searchable{/tr}(See this <a class='alert-link' href='http://dev.mysql.com/doc/refman/5.7/en/fulltext-stopwords.html'>{tr}link{/tr}</a> {tr} for full list) {/tr}</br>
						<b>{tr}Lucene(PHP implementation){/tr}: </b></br>
						{tr}Advantages{/tr}:{tr}Overcomes limitations of MySQL search mentioned above. Comes built in with Tiki{/tr}.</br>
						{tr}Disadvantages{/tr}:{tr}Slower performance. May not work well with the most basic server setups and because the index is stored on disk it is more prone to file permissions problems and other server configuration issues{/tr}.</br>
						<b>{tr}ElasticSearch{/tr}:</b></br>
						{tr}Advantages{/tr}:{tr}Most advanced, fast and scalable search engine. Enables some very advanced/new features of Tiki{/tr}.</br>
						{tr}Disadvantages{/tr}:{tr}Needs to be separately installed from Tiki and requires more configuration{/tr} (See this <a class='alert-link' href='http://doc.tiki.org/ElasticSearch'>{tr}link{/tr}</a> {tr}for more information) {/tr}</br>
					{/remarksbox}

					{if! empty($engine_info)}
						<divclass="adminoptionboxchild">
							<ul>
								{foreachfrom=$engine_info key=property item=value}
									<li><strong>{$property|escape}:</strong>{$value|escape}</li>
								{/foreach}
							</ul>
						</div>
					{/if}
					<divclass="adminoptionboxchild unified_engine_childcontainer lucene">
						{preferencename="unified_lucene_highlight"}
						{preferencename=unified_parse_results}
						{preferencename="unified_lucene_default_operator"}

						<fieldset>
							<legend>{tr}SearchEngine Settings{/tr}</legend>
							{preferencename="unified_lucene_location"}
							{preferencename="unified_lucene_max_result"}
							{preferencename="unified_lucene_max_resultset_limit"}
							{preferencename="unified_lucene_terms_limit"}
							{preferencename="unified_lucene_max_buffered_docs"}
							{preferencename="unified_lucene_max_merge_docs"}
							{preferencename="unified_lucene_merge_factor"}
						</fieldset>
					</div>

					<divclass="adminoptionboxchild unified_engine_childcontainer elastic">
						{preferencename="unified_elastic_url"}
						{preferencename="unified_elastic_index_prefix"}
						{preferencename="unified_elastic_index_current"}
					</div>

					{preferencename=unified_excluded_categories}
					{preferencename=unified_excluded_plugins}

					{preferencename=unified_exclude_all_plugins}
					<divclass="adminoptionboxchild" id="unified_exclude_all_plugins_childcontainer">
						{preferencename=unified_included_plugins}
					</div>

					{preferencename=unified_forum_deepindexing}

					{preferencename=unified_tokenize_version_numbers}

					{preferencename=unified_field_weight}
					{preferencename=unified_default_content}
					{preferencename=unified_user_cache}

					{preferencename=unified_cached_formatters}

					{preferencename=unified_trackerfield_keys}
					{preferencename=unified_add_to_categ_search}
				</div>
			</fieldset>

			<fieldset>
				<legend>
					{tr}BasicSearch{/tr} {help url="Search"}
				</legend>
				{preferencename=feature_search_fulltext}
				<divclass="adminoptionboxchild" id="feature_search_fulltext_childcontainer">				
					{preferencename=feature_referer_highlight}

					{preferencename=feature_search_show_forbidden_obj}
					{preferencename=feature_search_show_forbidden_cat}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Features{/tr}</legend>
				{preferencename=search_autocomplete}
			</fieldset>

			<fieldset>
				<legend>{tr}Forumsearches{/tr}</legend>
				{preferencename=feature_forums_name_search}
				{preferencename=feature_forums_search}
				{preferencename=feature_forum_content_search}
				<divclass="adminoptionboxchild" id="feature_forum_content_search_childcontainer">
					{preferencename=feature_forum_local_tiki_search}
					{preferencename=feature_forum_local_search}
				</div>
			</fieldset>

		{/tab}

		{tabname="{tr}Search Results{/tr}"}
			<h2>{tr}SearchResults{/tr}</h2>
			{preferencename=search_use_facets}
			{preferencename=category_custom_facets}

			<fieldset>
				<legend>{tr}Selectthe items to display on the search results page:{/tr}</legend>
				{preferencename=search_default_interface_language}
				{preferencename=search_default_where}
				{preferencename=search_show_category_filter}
				{preferencename=search_show_tag_filter}
				{preferencename=feature_search_show_object_filter}
				{preferencename=search_show_sort_order}
				{preferencename=feature_search_show_search_box}
			</fieldset>
			<fieldset>
				<legend>{tr}Selectthe information to display for each result:{/tr}</legend>
				{preferencename=feature_search_show_visit_count}
				{preferencename=feature_search_show_pertinence}
				{preferencename=feature_search_show_object_type}
				{preferencename=feature_search_show_last_modification}
				{preferencename=search_parsed_snippet}
			</fieldset>
		{/tab}

		{tabname="{tr}Stored Search{/tr}"}
			<h2>{tr}StoredSearch{/tr}</h2>
			{preferencename=storedsearch_enabled}
		{/tab}

		{tabname="{tr}Tools{/tr}"}
			<h2>{tr}Tools{/tr}</h2>
			<ahref="tiki-report_string_in_db.php">{tr}Report all occurences of a string in any table{/tr}</a><br>
		{/tab}

	{/tabset}
	<br>{*I cheated. *}
	<divclass="row">
		<divclass="form-group col-lg-12 clearfix">
			<divclass="text-center">
				<inputtype="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
