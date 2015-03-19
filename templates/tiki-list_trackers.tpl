{* $Id$ *}
{extends "layout_view.tpl"}

{block name="title"}
	{title help="Trackers" admpage="trackers"}{tr}Trackers{/tr}{/title}
{/block}

{block name="navigation"}
	{if $tiki_p_admin_trackers eq 'y'}
		<div class="form-group">{* Class provides 15px bottom margin. *}
			<a class="btn btn-default" href="{bootstrap_modal controller=tracker action=replace}">
				{icon name="create"} {tr}Create{/tr}
			</a>
			<a class="btn btn-default" href="{bootstrap_modal controller=tracker action=duplicate}">
				{icon name="copy"} {tr}Duplicate{/tr}
			</a>
			<div class="btn-group">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					{icon name="import"} {tr}Import{/tr}
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li>
						<a href="{bootstrap_modal controller=tracker action=import}">
							{tr}Import Structure{/tr}
						</a>
					</li>
					<li>
						<a href="{bootstrap_modal controller=tracker action=import_profile}">
							{tr}Import From Profile/YAML{/tr}
						</a>
					</li>
					{if $prefs.tracker_tabular_enabled eq 'y' && $tiki_p_admin_trackers eq 'y'}
						<li>
							<a href="{service controller=tabular action=manage}">
								{tr}Manage Tabular Formats{/tr}
							</a>
						</li>
					{/if}
				</ul>
			</div>
			{if $prefs.tracker_remote_sync eq 'y'}
				<a class="btn btn-default" href="{service controller=tracker_sync action=clone_remote}">
				{icon name="clone"} {tr}Clone remote{/tr}
				</a>
			{/if}
		</div>
	{/if}
{/block}

{block name="content"}
	<a name="view"></a>
	{if ($trackers) or ($find)}
		{include autocomplete='trackername' file='find.tpl' filters=''}
		{if ($find) and ($trackers)}
			<h4 class="find-results">{tr}Results{/tr} <span class="label label-default">{$trackers|@count}</span></h4>
		{/if}
	{/if}
	<div class="table-responsive">
		<table class="table table-condensed table-hover">
			<tr>
				<th>{self_link _sort_arg='sort_mode' _sort_field='trackerId'}{tr}Id{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Last Modified{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='items'}{tr}Items{/tr}{/self_link}</th>
				<th></th>
			</tr>

			{foreach from=$trackers item=tracker}
				<tr>
					<td class="id">
						{$tracker.trackerId|escape}
					</td>
					<td class="text">
						<a
							class="tips"
							title="{$tracker.name|escape}:{tr}View{/tr}"
							href="tiki-view_tracker.php?trackerId={$tracker.trackerId}"
						>
							{$tracker.name|escape}
						</a>
						<div class="description help-block">
							{if $tracker.descriptionIsParsed eq 'y'}
								{wiki}{$tracker.description}{/wiki}
							{else}
								{$tracker.description|escape|nl2br}
							{/if}
						</div>
					</td>
					<td class="date">{$tracker.created|tiki_short_date}</td>
					<td class="date">{$tracker.lastModif|tiki_short_datetime}</td>
					<td class="text-center">
						<a
							class="tips"
							title="{$tracker.name|escape}:{tr}View{/tr}"
							href="tiki-view_tracker.php?trackerId={$tracker.trackerId}"
						>
							<span class="badge">
								{$tracker.items|escape}
							</span>
						</a>
					</td>
					<td class="action">
						{capture name=tracker_actions}
							{strip}
								{if $tracker.permissions->export_tracker}
									<a onclick="$('[data-toggle=popover]').popover('hide');"
										data-toggle="modal"
										data-backdrop="static"
										data-target="#bootstrap-modal"
										href="{service controller=tracker action=export trackerId=$tracker.trackerId modal=1}"
									>
										{icon name='export' _menu_text='y' _menu_icon='y' alt="{tr}Export{/tr}"}
									</a>
								{/if}
								{if $tracker.permissions->admin_trackers}
									<a onclick="$('[data-toggle=popover]').popover('hide');"
										data-toggle="modal"
										data-backdrop="static"
										data-target="#bootstrap-modal"
										href="{service controller=tracker action=import_items trackerId=$tracker.trackerId modal=1}"
									>
										{icon name='import' _menu_text='y' _menu_icon='y' alt="{tr}Import{/tr}"}
									</a>
									<a onclick="$('[data-toggle=popover]').popover('hide');"
										data-toggle="modal"
										data-backdrop="static"
										data-target="#bootstrap-modal"
										href="{service controller=tracker_todo action=view trackerId=$tracker.trackerId modal=1}"
									>
										{icon name='calendar' _menu_text='y' _menu_icon='y' alt="{tr}Events{/tr}"}
									</a>
								{/if}
								<a href="tiki-view_tracker.php?trackerId={$tracker.trackerId}">
									{icon name='view' _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
								</a>
								{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
									<a href="tiki-object_watches.php?objectId={$tracker.trackerId}&amp;watch_event=tracker_modified&amp;objectType=tracker&amp;objectName={$tracker.name|escape:"url"}&amp;objectHref={'tiki-view_tracker.php?trackerId='|cat:$tracker.trackerId|escape:"url"}">
										{icon name='watch-group' _menu_text='y' _menu_icon='y' alt="{tr}Group monitor{/tr}"}
									</a>
								{/if}
								{if $prefs.feature_user_watches eq 'y' and $tracker.permissions->watch_trackers and $user}
									{if $tracker.watched}
										<a href="tiki-view_tracker.php?trackerId={$tracker.trackerId}&amp;watch=stop">
											{icon name='stop-watching' _menu_text='y' _menu_icon='y' alt="{tr}Stop monitoring{/tr}"}
										</a>
									{else}
										<a href="tiki-view_tracker.php?trackerId={$tracker.trackerId}&amp;watch=add">
											{icon name='watch' _menu_text='y' _menu_icon='y' alt="{tr}Monitor{/tr}"}
										</a>
									{/if}
								{/if}
								{if $prefs.feed_tracker eq "y"}
									<a href="tiki-tracker_rss.php?trackerId={$tracker.trackerId}">
										{icon name='rss' _menu_text='y' _menu_icon='y' alt="{tr}Feed{/tr}"}
									</a>
								{/if}
								{if $prefs.feature_search eq 'y'}
									<a href="tiki-searchindex.php?filter~tracker_id={$tracker.trackerId|escape}">
										{icon name='search' _menu_text='y' _menu_icon='y' alt="{tr}Search{/tr}"}
									</a>
								{/if}

								{if $tracker.permissions->admin_trackers}
									<a href="tiki-admin_tracker_fields.php?trackerId={$tracker.trackerId}">
										{icon name='trackerfields' _menu_text='y' _menu_icon='y' alt="{tr}Fields{/tr}"}
									</a>
									<a href="{service controller=tracker action=replace trackerId=$tracker.trackerId modal=true}"
										data-toggle="modal"
										data-backdrop="static"
										data-target="#bootstrap-modal"
										onclick="$('[data-toggle=popover]').popover('hide');"
									>
										{icon name='settings' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>
									{permission_link mode=text type=tracker permType=trackers id=$tracker.trackerId}
									{* can't get this to work properly for some reason. the remove one just below it works fine
										items can be deleted from the item listing of the tracker itself until this is fixed
									{if $tracker.items > 0}
										<a href="{service controller=tracker action=clear trackerId=$tracker.trackerId}" class="clear confirm-prompt">
											{icon name='trash' _menu_text='y' _menu_icon='y' alt="{tr}Clear{/tr}"}
										</a>
									{/if}
									*}
									<a href="{service controller=tracker action=remove trackerId=$tracker.trackerId}"
										class="remove confirm-prompt"
									>
										{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Delete{/tr}"}
									</a>
								{/if}
							{/strip}
						{/capture}
						<a class="tips"
						   title="{tr _0=$tracker.name|escape}Actions for tracker %0{/tr}"
						   href="#" {popup trigger="click" fullhtml="1" center=true text=$smarty.capture.tracker_actions|escape:"javascript"|escape:"html"}
						   style="padding:0; margin:0; border:0"
								>
							{icon name='wrench'}
						</a>
					</td>
				</tr>
			{foreachelse}
				{if $find}
					{norecords _colspan=6 _text="No records found with: $find"}
				{else}
					{norecords _colspan=6}
				{/if}
			{/foreach}
		</table>
	</div>
	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

{jq}
$('.remove.confirm-prompt').requireConfirm({
	message: "{tr}Do you really remove this tracker?{/tr}",
	success: function (data) {
		$(this).closest('tr').remove();
	}
});
$('.clear.confirm-prompt').requireConfirm({
	message: "{tr}Do you really want to clear all the items from this tracker? (N.B. there is no undo and notifications will not be sent){/tr}",
	success: function (data) {
		history.go(0);	// reload
	}
});
{/jq}

{/block}
