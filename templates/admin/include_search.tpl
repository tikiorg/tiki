{* $Id$ *}

{if $prefs.feature_search_stats eq 'y'}
	{remarksbox type="tip" title="{tr}Tip{/tr}"}
		{tr}Search statistics{/tr} {tr}can be seen on page{/tr} <a class='alert-link' target='tikihelp' href='tiki-search_stats.php'>{tr}Search statistics{/tr}</a> {tr}in Admin menu{/tr}
	{/remarksbox}
{/if}

{if $prefs.feature_file_galleries eq 'y'}
	{remarksbox type="tip" title="{tr}Tip{/tr}"}
		{tr}Also see the Search Indexing tab:{/tr} <a class='alert-link' target='tikihelp' href='tiki-admin.php?page=fgal'>{tr}File Gallery admin panel{/tr}</a>
	{/remarksbox}
{/if}


<form class="form-horizontal" action="tiki-admin.php?page=search" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<input type="hidden" name="searchprefs">

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			{if $prefs.feature_search eq 'y'}
				<a role="link" href="tiki-searchindex.php" class="btn btn-link">{icon name="search"} {tr}Search{/tr}</a>
				<a role="link" href="{bootstrap_modal controller=search action=rebuild}" class="btn btn-link">{icon name="cog"} {tr}Rebuild Index{/tr}</a>
			{/if}
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
	{tabset name=admin_search}
		{tab name="{tr}General Settings{/tr}"}
			<h2>{tr}General Settings{/tr}</h2>

			<fieldset>
				<legend>
					{tr}Search{/tr}
				</legend>
				{remarksbox type=tip title="{tr}About the Unified Index{/tr}"}
				{tr}The Unified Index provides many underlying features for Tiki, including object selectors for translations amongst other things.{/tr}
				{tr}Disabling this will cause some parts of Tiki to be unavailable.{/tr}<br>
					<a href="http://doc.tiki.org/Unified+Index">{tr}Find out more about it here.{/tr}</a>
				{/remarksbox}

				{preference name=feature_search visible="always"}
				<div class="adminoptionboxchild" id="feature_search_childcontainer">

					{preference name=feature_search_stats}
					{preference name=user_in_search_result}
					{preference name="unified_incremental_update"}

					{preference name="allocate_memory_unified_rebuild"}
					{preference name="allocate_time_unified_rebuild"}

					{preference name="unified_engine"}

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

					{if $smod_params.advanced_search_option eq 'y'}
					{remarksbox type=tip title="{tr}About Unified search engines{/tr}"}
						<b>{tr}MySQL full-text search{/tr}: </b><br>
					{tr}Advantages{/tr}: {tr}Fast performance. Works out of the box with Tiki and even on most basic server setups{/tr}.<br>
					{tr}Disadvantages{/tr}: {tr}Many common words (such as "first", "second", and "third" are not searchable unless MySQL configuration is modified). Only the first 65,535 characters (about 8000 words) of long pieces of content are searchable{/tr}(See this <a class='alert-link' href='http://dev.mysql.com/doc/refman/5.7/en/fulltext-stopwords.html'>{tr}link{/tr}</a> {tr} for full list) {/tr}<br>
						<b>{tr}Lucene (PHP implementation){/tr}: </b><br>
					{tr}Advantages{/tr}: {tr}Overcomes limitations of MySQL search mentioned above. Comes built in with Tiki{/tr}.<br>
					{tr}Disadvantages{/tr}: {tr}Slower performance. May not work well with the most basic server setups and because the index is stored on disk it is more prone to file permissions problems and other server configuration issues{/tr}.<br>
						<b>{tr}ElasticSearch{/tr}: </b><br>
					{tr}Advantages{/tr}: {tr}Most advanced, fast and scalable search engine. Enables some very advanced/new features of Tiki{/tr}.<br>
					{tr}Disadvantages{/tr}: {tr}Needs to be separately installed from Tiki and requires more configuration{/tr} (See this <a class='alert-link' href='http://doc.tiki.org/ElasticSearch'>{tr}link{/tr}</a> {tr}for more information) {/tr}<br>
					{/remarksbox}
					{/if}

					<div class="adminoptionboxchild unified_engine_childcontainer elastic">
						{preference name="unified_elastic_url"}
						{preference name="unified_elastic_index_prefix"}
						{preference name="unified_elastic_index_current"}
					</div>

					{preference name="unified_lucene_default_operator"}
					{preference name=unified_excluded_categories}
					{preference name=unified_excluded_plugins}

					{preference name=unified_exclude_all_plugins}
					<div class="adminoptionboxchild" id="unified_exclude_all_plugins_childcontainer">
						{preference name=unified_included_plugins}
					</div>

					{preference name=unified_forum_deepindexing}

					{preference name=unified_tokenize_version_numbers}
					<div class="adminoptionboxchild unified_engine_childcontainer elastic">
						{preference name="unified_elastic_camel_case"}
					</div>

					{preference name=unified_field_weight}
					{preference name=unified_default_content}
					{preference name=unified_user_cache}

					{preference name=unified_cached_formatters}

					{preference name=unified_trackerfield_keys}
					{preference name=unified_add_to_categ_search}
					{preference name=unified_trim_sorted_search}

					{preference name=unified_stopwords}
				</div>
			</fieldset>

			<fieldset>
				<legend>
					{tr}Legacy Search{/tr} {help url="Search"}
				</legend>
				{preference name=feature_search_fulltext}
				<div class="adminoptionboxchild" id="feature_search_fulltext_childcontainer">
					{preference name=feature_referer_highlight}

					{preference name=feature_search_show_forbidden_obj}
					{preference name=feature_search_show_forbidden_cat}
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
			<h2>{tr}Search Results{/tr}</h2>
			{preference name=search_use_facets}
			{preference name=search_facet_default_amount}
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

		{tab name="{tr}Stored Search{/tr}"}
			<h2>{tr}Stored Search{/tr}</h2>
			{preference name=storedsearch_enabled}
		{/tab}

		{tab name="{tr}Federated Search{/tr}"}
			<h2>{tr}Federated Search{/tr}</h2>
			{preference name=federated_enabled}
			{preference name=federated_elastic_url}

			<h3>{tr}Configuration{/tr}</h3>
			<ul>
				<li><a href="tiki-admin_external_wikis.php">{tr}External Wiki{/tr}</a></li>
				<li><a href="{bootstrap_modal controller=search_manifold action=check}">{tr}ManifoldCF Configuration Checker{/tr}</a></li>
			</ul>
		{/tab}

		{tab name="{tr}Tools{/tr}"}
			<h2>{tr}Tools{/tr}</h2>
			<a href="tiki-report_string_in_db.php">{tr}Report all occurences of a string in any table{/tr}</a><br>
		{/tab}

	{/tabset}
	<br>{* I cheated. *}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
