{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}" /></div><div class="adminWizardIconright"><img src="img/icons/large/xfce4-appfinder48x48.png" alt="{tr}Set up your Search features{/tr}"></div>
{tr}There are two search systems in Tiki that use different search engines: <strong>Basic Search</strong> and <strong>Advanced Search</strong>{/tr}.
{tr}The Advanced Search generally provides better results, but is more demanding on the server (it creates a search index,
	updated either incrementally or based on a cron job configured elsewhere){/tr}.
<br/><br/>
<div class="adminWizardContent">
<table style="width:100%">
    <tr>
        <td style="width:48%">
			<fieldset>
				<legend>{tr}Basic Search{/tr} {help url="Search"}</legend>
				{tr}Uses MySQL Full-Text Search{/tr}.
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
        </td>
        <td style="width:4%">
            &nbsp;
        </td>
        <td style="width:48%">			
			<fieldset>
				<legend>{tr}Other settings{/tr}</legend>
					{preference name=search_default_interface_language}
					{preference name=search_default_where}
					{if $prefs.feature_file_galleries eq 'y'}<br>
						<em>{tr}Also see the Search Indexing tab here:{/tr} <a class='rbox-link' target='tikihelp' href='tiki-admin.php?page=fgal'>{tr}File Gallery admin panel{/tr}</a></em>
					{/if}
			</fieldset>
        </td>
    </tr>
</table>

<em>{tr}See also{/tr} <a href="tiki-admin.php?page=search&amp;cookietab=1" target="_blank">{tr}Search admin panel{/tr}</a> & <a href="https://doc.tiki.org/Search" target="_blank">{tr}Search in doc.tiki.org{/tr}</a></em>

</div>
