{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
    {icon name="admin_search" size=3 iclass="pull-right"}
    {tr}There are two search systems in Tiki that use different search engines: <strong>Basic Search</strong> and <strong>Advanced Search</strong>{/tr}.
    {tr}The Advanced Search generally provides better results, but is more demanding on the server (it creates a search index, updated either incrementally or based on a cron job configured elsewhere){/tr}.
	<div class="media-body">
		<br/><br/>
		<div class="row">
			<div class="col-md-6">
				<fieldset>
					<legend>{tr}Basic Search{/tr} {help url="Search"}</legend>
					{tr}Uses MySQL full-text search{/tr}.
					{tr}If enabled, the search module and search feature in the main application menu will use it by default, even if 'Advanced Search' is also enabled below{/tr}.
					{preference name=feature_search_fulltext}
					<div class="adminoptionboxchild" id="feature_search_fulltext_childcontainer">
						{preference name=feature_referer_highlight}
					</div>
				</fieldset>
				<fieldset>
					<legend>{tr}Advanced Search{/tr}</legend>
					{tr}Uses Unified Search Index with a specified search engine{/tr}. {tr}If you have issues with Advanced Search, simply revert to Basic Search{/tr}.
					{tr}Unified Search is required by a number of other features, e.g. the community friendship network{/tr}
					{preference name=feature_search visible="always"}
					<div class="adminoptionboxchild" id="feature_search_childcontainer">
						{preference name="unified_incremental_update"}
						{preference name="unified_engine"}
						<div class="adminoptionboxchild unified_engine_childcontainer lucene">
							{preference name="unified_lucene_default_operator"}
						</div>
						<div class="adminoptionboxchild unified_engine_childcontainer elastic">
							{preference name="unified_elastic_url"}
							{preference name="unified_elastic_index_prefix"}
							{preference name="unified_elastic_index_current"}
						</div>
					</div>
				</fieldset>
			</div>
			<div class="col-md-6">
				<fieldset>
					<legend>{tr}Other settings{/tr}</legend>
					{preference name=search_default_interface_language}
					{preference name=search_default_where}
					{if $prefs.feature_file_galleries eq 'y'}<br>
						<em>{tr}Also see the Search Indexing tab here:{/tr} <a class='rbox-link' target='tikihelp' href='tiki-admin.php?page=fgal'>{tr}File Gallery admin panel{/tr}</a></em>
					{/if}
				</fieldset>
			</div>
		</div>
		<em>{tr}See also{/tr} <a href="tiki-admin.php?page=search&amp;cookietab=1" target="_blank">{tr}Search admin panel{/tr}</a> & <a href="https://doc.tiki.org/Search" target="_blank">{tr}Search in doc.tiki.org{/tr}</a></em>
	</div>
</div>
