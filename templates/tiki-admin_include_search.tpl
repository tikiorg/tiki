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

				{preference name=feature_search}
				<div class="adminoptionboxchild" id="feature_search_childcontainer">				
					{preference name="unified_incremental_update"}
					{preference name="unified_engine"}
					<div class="adminoptionboxchild unified_engine_childcontainer lucene">
						{preference name="unified_lucene_location"}
					</div>

					{preference name=unified_field_weight}
					{preference name=unified_default_content}

					<a href="tiki-admin.php?page=search&amp;rebuild=now">{tr}Rebuild Index{/tr}</a>
					{if !empty($stat)}
						{remarksbox type='feedback' title="{tr}Indexation{/tr}"}<ul>
						{foreach from=$stat key=what item=nb}
							<li>{$what|escape}: {$nb|escape}</li>
						{/foreach}
						</ul>{/remarksbox}
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

					<fieldset>
						<legend>{tr}Permissions{/tr}</legend>
						{icon _id=information} {tr}Enabling these options will improve performance, but may show forbidden results{/tr}.

						{preference name=feature_search_show_forbidden_obj}
						{preference name=feature_search_show_forbidden_cat}
					</fieldset>
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
