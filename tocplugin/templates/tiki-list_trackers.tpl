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
				{icon name="copy"} {tr}Clone remote{/tr}
				</a>
			{/if}
		</div>
	{/if}
{/block}

{block name="content"}
	<a id="view"></a>
	{if ($trackers) or ($find)}
		{include autocomplete='trackername' file='find.tpl' filters=''}
		{if ($find) and ($trackers)}
			<h4 class="find-results">{tr}Results{/tr} <span class="label label-default">{$trackers|@count}</span></h4>
		{/if}
	{/if}

	{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
	{if $prefs.javascript_enabled !== 'y'}
		{$js = 'n'}
		{$libeg = '<li>'}
		{$liend = '</li>'}
	{else}
		{$js = 'y'}
		{$libeg = ''}
		{$liend = ''}
	{/if}

	<div class="{if $js === 'y'}table-responsive{/if}"> {*the table-responsive class cuts off dropdown menus *}
		<table class="table table-condensed table-hover table-striped">
			<tr>
				<th>{self_link _sort_arg='sort_mode' _sort_field='trackerId'}{tr}Id{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Last modified{/tr}{/self_link}</th>
				<th class="text-right">{self_link _sort_arg='sort_mode' _sort_field='items'}{tr}Items{/tr}{/self_link}</th>
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
							href="{$tracker.trackerId|sefurl:'tracker'}"
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
					<td class="integer">
						<a
							class="tips"
							title="{$tracker.name|escape}:{tr}View{/tr}"
							href="tiki-view_tracker.php?trackerId={$tracker.trackerId}"
						>

								{$tracker.items|escape}

						</a>
					</td>
					<td class="action">
						{capture name=tracker_actions}
							{strip}
								{if $tracker.permissions->export_tracker}
									{$libeg}<a onclick="$('[data-toggle=popover]').popover('hide');"
										data-toggle="modal"
										data-backdrop="static"
										data-target="#bootstrap-modal"
										href="{service controller=tracker action=export trackerId=$tracker.trackerId modal=1}"
									>
										{icon name='export' _menu_text='y' _menu_icon='y' alt="{tr}Export{/tr}"}
									</a>{$liend}
								{/if}
								{if $tracker.permissions->admin_trackers}
									{$libeg}<a onclick="$('[data-toggle=popover]').popover('hide');"
										data-toggle="modal"
										data-backdrop="static"
										data-target="#bootstrap-modal"
										href="{service controller=tracker action=import_items trackerId=$tracker.trackerId modal=1}"
									>
										{icon name='import' _menu_text='y' _menu_icon='y' alt="{tr}Import{/tr}"}
									</a>{$liend}
									{$libeg}<a onclick="$('[data-toggle=popover]').popover('hide');"
										data-toggle="modal"
										data-backdrop="static"
										data-target="#bootstrap-modal"
										href="{service controller=tracker_todo action=view trackerId=$tracker.trackerId modal=1}"
									>
										{icon name='calendar' _menu_text='y' _menu_icon='y' alt="{tr}Events{/tr}"}
									</a>{$liend}
								{/if}
								{$libeg}<a href="tiki-view_tracker.php?trackerId={$tracker.trackerId}">
									{icon name='view' _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
								</a>{$liend}
								{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
									{$libeg}<a href="tiki-object_watches.php?objectId={$tracker.trackerId}&amp;watch_event=tracker_modified&amp;objectType=tracker&amp;objectName={$tracker.name|escape:"url"}&amp;objectHref={'tiki-view_tracker.php?trackerId='|cat:$tracker.trackerId|escape:"url"}">
										{icon name='watch-group' _menu_text='y' _menu_icon='y' alt="{tr}Group monitor{/tr}"}
									</a>{$liend}
								{/if}
								{if $prefs.feature_user_watches eq 'y' and $tracker.permissions->watch_trackers and $user}
									{if $tracker.watched}
										{$libeg}<a href="tiki-view_tracker.php?trackerId={$tracker.trackerId}&amp;watch=stop">
											{icon name='stop-watching' _menu_text='y' _menu_icon='y' alt="{tr}Stop monitoring{/tr}"}
										</a>{$liend}
									{else}
										{$libeg}<a href="tiki-view_tracker.php?trackerId={$tracker.trackerId}&amp;watch=add">
											{icon name='watch' _menu_text='y' _menu_icon='y' alt="{tr}Monitor{/tr}"}
										</a>{$liend}
									{/if}
								{/if}
								{if $prefs.feed_tracker eq "y"}
									{$libeg}<a href="tiki-tracker_rss.php?trackerId={$tracker.trackerId}">
										{icon name='rss' _menu_text='y' _menu_icon='y' alt="{tr}Feed{/tr}"}
									</a>{$liend}
								{/if}
								{if $prefs.feature_search eq 'y'}
									{$libeg}<a href="tiki-searchindex.php?filter~tracker_id={$tracker.trackerId|escape}">
										{icon name='search' _menu_text='y' _menu_icon='y' alt="{tr}Search{/tr}"}
									</a>{$liend}
								{/if}

								{if $tracker.permissions->admin_trackers}
									{$libeg}<a href="tiki-admin_tracker_fields.php?trackerId={$tracker.trackerId}">
									{icon name='th-list' _menu_text='y' _menu_icon='y' alt="{tr}Fields{/tr}"}
									</a>{$liend}
									{$libeg}<a href="{service controller=tracker action=replace trackerId=$tracker.trackerId modal=true}"
										data-toggle="modal"
										data-backdrop="static"
										data-target="#bootstrap-modal"
										onclick="$('[data-toggle=popover]').popover('hide');"
									>
										{icon name='settings' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>{$liend}
									{$libeg}{permission_link mode=text type=tracker permType=trackers id=$tracker.trackerId}{$liend}
									{if $tracker.items > 0}
										{$libeg}<a href="{service controller=tracker action=clear trackerId=$tracker.trackerId}" class="clear confirm-prompt">
											{icon name='trash' _menu_text='y' _menu_icon='y' alt="{tr}Clear{/tr}"}
										</a>{$liend}
									{/if}
									{$libeg}<a href="{service controller=tracker action=remove trackerId=$tracker.trackerId}"
										class="remove confirm-prompt"
									>
										{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Delete{/tr}"}
									</a>{$liend}
								{/if}
							{/strip}
						{/capture}
						{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a
							class="tips"
							title="{tr}Actions{/tr}"
							href="#"
							{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.tracker_actions|escape:"javascript"|escape:"html"}{/if}
							style="padding:0; margin:0; border:0"
						>
							{icon name='wrench'}
						</a>
						{if $js === 'n'}
							<ul class="dropdown-menu" role="menu">{$smarty.capture.tracker_actions}</ul></li></ul>
						{/if}
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
$(document).on('click', '.remove.confirm-prompt', $.clickModal({
		message: "{tr}Do you really remove this tracker?{/tr}",
		success: function (data) {
			history.go(0);	// reload
		}
	}));
$(document).on('click', '.clear.confirm-prompt', $.clickModal({
		message: "{tr}Do you really want to clear all the items from this tracker? (N.B. there is no undo and notifications will not be sent){/tr}",
		success: function (data) {
			history.go(0);	// reload
		}
	}));
{/jq}

{/block}
