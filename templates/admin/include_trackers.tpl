<form class="form-horizontal" action="tiki-admin.php?page=trackers" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="button" class="btn btn-link" href="tiki-list_trackers.php" title="{tr}List{/tr}">
				{icon name="list"} {tr}Trackers{/tr}
			</a>
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="trkset" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>

	{tabset}

		{tab name="{tr}Settings{/tr}"}
			<h2>{tr}Settings{/tr}</h2>
			<fieldset>
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_trackers visible="always"}
			</fieldset>
			<fieldset class="table">
				<legend>{tr}Tracker settings{/tr}</legend>
				{preference name=feature_reports}
				{preference name="tracker_remote_sync"}
				{preference name="tracker_tabular_enabled"}
				{preference name="tracker_clone_item"}
				{preference name=allocate_memory_tracker_export_items}
				{preference name=allocate_time_tracker_export_items}
				{preference name=allocate_time_tracker_clear_items}

				{preference name=ajax_inline_edit}
				<div class="adminoptionboxchild" id="ajax_inline_edit_childcontainer">
					{preference name=ajax_inline_edit_trackerlist}
				</div>
				{preference name=tracker_show_comments_below}
				{preference name=tracker_legacy_insert}
				{preference name=tracker_status_in_objectlink}
				{preference name=tracker_always_notify}
				{preference name=feature_sefurl_tracker_prefixalias}
				{preference name=tracker_prefixalias_on_links}
			</fieldset>
			<fieldset class="table">
				<legend>{tr}Field settings{/tr}</legend>
				{preference name=user_selector_threshold}
				{preference name=user_selector_realnames_tracker}
				{preference name=tiki_object_selector_threshold}
				{preference name="tracker_refresh_itemlink_detail"}
				{preference name=fgal_tracker_existing_search}
				{preference name=unified_trackerfield_keys}
				{preference name=tracker_change_field_type}
			</fieldset>

			<fieldset class="admin">
				<legend>{tr}Linked wiki pages{/tr}</legend>
				{remarksbox type="tip" title="{tr}Tip{/tr}"}
					{tr}Wiki pages are linked to tracker items, and their page names to tracker fields, via the tiki.wiki.linkeditem and tiki.wiki.linkedfield relations. You need to be familiar with the Relations tracker field or use the outputwiki option in the TRACKER plugin to make use of these features.{/tr}
				{/remarksbox}
				{preference name=tracker_wikirelation_synctitle}
				{preference name=tracker_wikirelation_redirectpage}
			</fieldset>

			<fieldset class="table">
				<legend>{tr}Tracker attachment preferences{/tr}</legend>
					<table class="table">
						<tr>
							<td>
								{tr}Use database to store files:{/tr}
							</td>
							<td>
								<input type="radio" name="t_use_db" value="y" {if $prefs.t_use_db eq 'y'}checked="checked"{/if}/>
							</td>
						</tr>

						<tr>
							<td>
								{tr}Use a directory to store files:{/tr}</td>
							<td>
								<input type="radio" name="t_use_db" value="n" {if $prefs.t_use_db eq 'n'}checked="checked"{/if}/> {tr}Path:{/tr}
								<br>
								<input type="text" name="t_use_dir" value="{$prefs.t_use_dir|escape}" size="50" />
							</td>
						</tr>

					</table>
			</fieldset>
		{/tab}

		{tab name="{tr}Plugins{/tr}"}
			<h2>{tr}Plugins{/tr}</h2>
			<fieldset class="table">
				<legend>{tr}Plugins{/tr}</legend>
				{preference name=wikiplugin_insert}
				<div class="adminoptionboxchild" id="wikiplugin_insert_childcontainer">
					{preference name=tracker_insert_allowed}
				</div>
				{preference name=wikiplugin_tracker}
				{preference name=wikiplugin_trackerlist}
				{preference name=wikiplugin_trackerfilter}
				{preference name=wikiplugin_trackerif}
				{preference name=wikiplugin_trackerstat}
				{preference name=wikiplugin_miniquiz}
				{preference name=wikiplugin_vote}
				{preference name=wikiplugin_trackercomments}
				{preference name=wikiplugin_trackeritemfield}
				{preference name=wikiplugin_trackerprefill}
				{preference name=wikiplugin_trackertimeline}
				{preference name=wikiplugin_trackertoggle}
				{preference name=wikiplugin_prettytrackerviews}
				{preference name=wikiplugin_trackerpasscode}
				{preference name=wikiplugin_trackeritemcopy}
				{preference name=wikiplugin_trackerquerytemplate}
			</fieldset>
		{/tab}

		{tab name="{tr}Field Types{/tr}"}
			<h2>{tr}Field Types{/tr}</h2>
			<fieldset class="table">
				<legend>{tr}Field Types{/tr}</legend>
				{foreach from=$fieldPreferences item=name}
					{preference name=$name}
				{/foreach}
			</fieldset>
		{/tab}

	{/tabset}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="trkset" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>


<fieldset>
	<legend>{tr}Tracker attachments{/tr}</legend>
	<div class="table">
		{if $attachements}
			<form action="tiki-admin.php?page=trackers" method="post">
				<input type="text" name="find" value="{$find|escape}" />
				<input type="submit" class="btn btn-default btn-sm" name="action" value="{tr}Find{/tr}" />
			</form>
		{/if}

		<div class="table-responsive">
			<table class="table">
				<tr>
					<th>
						<a href="tiki-admin.php?page=trackers&amp;sort_mode=user_{if $sort_mode eq 'attId'}asc{else}desc{/if}">{tr}ID{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=trackers&amp;sort_mode=user_{if $sort_mode eq 'user'}asc{else}desc{/if}">{tr}User{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=trackers&amp;sort_mode=filename_{if $sort_mode eq 'filename'}asc{else}desc{/if}">{tr}Name{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=trackers&amp;sort_mode=filesize_{if $sort_mode eq 'filesize'}asc{else}desc{/if}">{tr}Size{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=trackers&amp;sort_mode=filetype_{if $sort_mode eq 'filetype'}asc{else}desc{/if}">{tr}Type{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=trackers&amp;sort_mode=hits_{if $sort_mode eq 'hits'}asc{else}desc{/if}">{tr}dls{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=trackers&amp;sort_mode=itemId_{if $sort_mode eq 'itemId'}asc{else}desc{/if}">{tr}Item{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=trackers&amp;sort_mode=path_{if $sort_mode eq 'path'}asc{else}desc{/if}">{tr}Storage{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin.php?page=trackers&amp;sort_mode=created_{if $sort_mode eq 'created'}asc{else}desc{/if}">{tr}Created{/tr}</a>
					</th>
					<th>{tr}Switch storage{/tr}</th>
				</tr>

				{section name=x loop=$attachements}
					<tr class={cycle}>
						<td class="id"><a href="tiki-download_item_attachment.php?attId={$attachements[x].attId}" title="{tr}Download{/tr}">{$attachements[x].attId}</a></td>
						<td class="username">{$attachements[x].user}</td>
						<td class="text">{$attachements[x].filename}</td>
						<td class="integer">{$attachements[x].filesize|kbsize}</td>
						<td class="text">{$attachements[x].filetype}</td>
						<td class="integer">{$attachements[x].hits}</td>
						<td class="integer">{$attachements[x].itemId}</td>
						<td class="text">{if $attachements[x].path}file{else}db{/if}</td>
						<td class="date">{$attachements[x].created|tiki_short_date}</td>
						<td class="action">
							<a href="tiki-admin.php?page=trackers&amp;attId={$attachements[x].attId}&amp;action={if $attachements[x].path}move2db{else}move2file{/if}">
								{icon name='refresh' iclass='tips' title=":{tr}Switch storage{/tr}"}
							</a>
						</td>
					</tr>
				{sectionelse}
					{norecords _colspan=10}
				{/section}
			</table>
		</div>

		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
	</div>
	{if $attachements}
		<table>
			<tr>
				<td>
					<form action="tiki-admin.php?page=trackers" method="post">
						<input type="hidden" name="all2db" value="1" />
						<input type="submit" class="btn btn-default btn-sm" name="action" value="{tr}Change all to db{/tr}" />
					</form>
				</td>
				<td>
					<form action="tiki-admin.php?page=trackers" method="post">
						<input type="hidden" name="all2file" value="1" />
						<input type="submit" class="btn btn-default btn-sm" name="action" value="{tr}Change all to file{/tr}" />
					</form>
				</td>
			</tr>
		</table>
	{/if}
</fieldset>
