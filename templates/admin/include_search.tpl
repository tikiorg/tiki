{* $Id$ *}

{if $prefs.feature_search_stats eq 'y'}
	{remarksbox type="tip" title="{tr}Tip{/tr}"}
		{tr}Search stats{/tr} {tr}can be seen on page{/tr} <a class='rbox-link' target='tikihelp' href='tiki-search_stats.php'>{tr}Search stats{/tr}</a> {tr}in Admin menu{/tr}
	{/remarksbox}
{/if}


<form action="tiki-admin.php?page=search" method="post">
	<input type="hidden" name="searchprefs" />
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>

	{tabset name=admin_search}
		{tab name="{tr}General Settings{/tr}"}
			<fieldset>
				<legend>
					{tr}Unified Search{/tr}
				</legend>

				{preference name=feature_search visible="always"}
				<div class="adminoptionboxchild" id="feature_search_childcontainer">				
					{preference name="unified_engine"}
					<div class="adminoptionboxchild unified_engine_childcontainer lucene">
						{preference name="unified_incremental_update"}
						{preference name="unified_lucene_highlight"}
						{preference name="unified_lucene_location"}
						<fieldset>
							<legend>{tr}Search Engine Settings{/tr}</legend>
							{preference name="unified_lucene_max_result"}
							{preference name="unified_lucene_max_resultset_limit"}
							{preference name="unified_lucene_terms_limit"}
							{preference name="unified_lucene_max_buffered_docs"}
							{preference name="unified_lucene_max_merge_docs"}
							{preference name="unified_lucene_merge_factor"}
							{preference name="unified_lucene_default_operator"}
						</fieldset>
					</div>

					{preference name=unified_forum_deepindexing}
					
					{preference name=unified_tokenize_version_numbers}

					{preference name=unified_field_weight}
					{preference name=unified_default_content}
					{preference name=unified_user_cache}

					{preference name=unified_cached_formatters}

					{preference name=unified_trackerfield_keys}

					<h4>{tr}Index maintenance{/tr}</h4>
					<ul>
						<li><a href="tiki-admin.php?page=search&amp;optimize=now">{tr}Optimize{/tr}</a> {tr}From the command line:{/tr} <kbd>php lib/search/shell.php optimize</kbd></li>
						<li>
							<a href="tiki-admin.php?page=search&amp;rebuild=now" id="rebuild-link">{tr}Rebuild Index{/tr}</a> {tr}From the command line:{/tr} <kbd>php lib/search/shell.php rebuild</kbd><br />
							<label for="log-rebuild">{tr}Log rebuild?{/tr}</label>
							<input type="checkbox" id="log-rebuild" />
							<span class="description">{tr}Log file is saved as temp/Search_Indexer.log{/tr}</span>
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
					{if $queue_count > 0}
						<h5>{tr}Queue size:{/tr} {$queue_count}</h5>
						{tr}Process:{/tr}
						<ul>
							{if $queue_count > 10}
								<li><a  href="tiki-admin.php?page=search&amp;process=10">10</a></li>
							{/if}
							{if $queue_count > 20}
								<li><a  href="tiki-admin.php?page=search&amp;process=20">20</a></li>
							{/if}
							{if !empty($smarty.request.process) and $smarty.request.process eq 'all' and $prefs.javascript_enabled eq "y"}
								{jq} setTimeout(function() { history.go(0); }, 1000); {/jq}
								<li><strong><a  href="tiki-admin.php?page=search&amp;process=">{tr}Stop{/tr}</a></strong></li>
							{else}
								<li><em><a  href="tiki-admin.php?page=search&amp;process=all">{tr}All{/tr}</a></em> <br /><span class="description">{tr}Uses JavaScript to reload this page until queue is processed{/tr}</span></li>
							{/if}
						</ul>
					{/if}
				</div>
			</fieldset>
			<fieldset>
				<legend>
					{tr}MySQL Search (legacy){/tr}{help url="Search"}
				</legend>
				{preference name=feature_search_fulltext}
				<div class="adminoptionboxchild" id="feature_search_fulltext_childcontainer">				
					{preference name=feature_referer_highlight}
					{preference name=search_parsed_snippet}
					{preference name=feature_search_stats}

					{preference name=feature_search_show_forbidden_obj}
					{preference name=feature_search_show_forbidden_cat}
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Features{/tr}</legend>
				{preference name=search_autocomplete}
			</fieldset>

		{/tab}

		{tab name="{tr}Search Results{/tr}"}
			{preference name=search_default_interface_language}
			{preference name=search_default_where}
			{tr}Select the items to display on the search results page:{/tr}
			{preference name=feature_search_show_object_filter}
			{preference name=feature_search_show_search_box}
			{tr}Select the information to display for each result:{/tr}
			{preference name=feature_search_show_visit_count}
			{preference name=feature_search_show_pertinence}
			{preference name=feature_search_show_object_type}
			{preference name=feature_search_show_last_modification}
		{/tab}
	{/tabset}
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
