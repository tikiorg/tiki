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
			<th>{tr}Actions{/tr}</th>
		</tr>

		{foreach from=$trackers item=tracker}
			<tr>
				<td class="id">
					{$tracker.trackerId|escape}
				</td>
				<td class="text">
					<a class="tablename" href="tiki-view_tracker.php?trackerId={$tracker.trackerId}" title="{tr}View{/tr}">{$tracker.name|escape}</a>
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
				<td class="text-center"><a title="{tr}View{/tr}" href="tiki-view_tracker.php?trackerId={$tracker.trackerId}"><span class="badge">{$tracker.items|escape}</span></a></td>
				<td class="action">
					{if $tracker.permissions->export_tracker}
						<a title="{tr _0=$tracker.name|escape}Export %0{/tr}" data-toggle="modal" data-target="#bootstrap-modal" href="{service controller=tracker action=export trackerId=$tracker.trackerId modal=1}">{icon _id='disk' alt="{tr}Export{/tr}"}</a>
					{/if}
					{if $tracker.permissions->admin_trackers}
						<a title="{tr _0=$tracker.name|escape}Import in %0{/tr}" data-toggle="modal" data-target="#bootstrap-modal" href="{service controller=tracker action=import_items trackerId=$tracker.trackerId modal=1}">{icon _id='upload' alt="{tr}Import{/tr}"}</a>
						<a title="{tr _0=$tracker.name|escape}Events{/tr}" data-toggle="modal" data-target="#bootstrap-modal" href="{service controller=tracker_todo action=view trackerId=$tracker.trackerId modal=1}">{icon _id='clock' alt="{tr}Events{/tr}"}</a>
					{/if}
					<a title="{tr}View{/tr}" href="tiki-view_tracker.php?trackerId={$tracker.trackerId}">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>

					{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
					 	 <a href="tiki-object_watches.php?objectId={$tracker.trackerId}&amp;watch_event=tracker_modified&amp;objectType=tracker&amp;objectName={$tracker.name|escape:"url"}&amp;objectHref={'tiki-view_tracker.php?trackerId='|cat:$tracker.trackerId|escape:"url"}" class="icon">{icon _id='eye_group' alt="{tr}Group Monitor{/tr}"}</a>
					{/if}
					{if $prefs.feature_user_watches eq 'y' and $tracker.permissions->watch_trackers and $user}
						{if $tracker.watched}
							<a href="tiki-view_tracker.php?trackerId={$tracker.trackerId}&amp;watch=stop" title="{tr}Stop Monitor{/tr}">{icon _id='no_eye' alt="{tr}Stop Monitor{/tr}"}</a>
						{else}
							<a href="tiki-view_tracker.php?trackerId={$tracker.trackerId}&amp;watch=add" title="{tr}Monitor{/tr}">{icon _id='eye' alt="{tr}Monitor{/tr}"}</a>
						{/if}
					{/if}
				
					{if $prefs.feed_tracker eq "y"}
						<a href="tiki-tracker_rss.php?trackerId={$tracker.trackerId}">{icon _id='feed' alt="{tr}Feed{/tr}"}</a>
					{/if}
					
					{if $tracker.permissions->admin_trackers}
						<a title="{tr}Fields{/tr}" class="link" href="tiki-admin_tracker_fields.php?trackerId={$tracker.trackerId}">{icon _id='table' alt="{tr}Fields{/tr}"}</a>
						<a title="{tr}Edit{/tr}" class="edit" data-toggle="modal" data-target="#bootstrap-modal" href="{service controller=tracker action=replace trackerId=$tracker.trackerId modal=true}">{icon _id='pencil' alt="{tr}Edit{/tr}"}</a>
						{permission_link mode=icon type=tracker permType=trackers id=$tracker.trackerId title=$tracker.name}
						{if $tracker.items > 0}
							<a title="{tr}Clear{/tr}" class="link clear confirm-prompt" href="{service controller=tracker action=clear trackerId=$tracker.trackerId}">{icon _id='bin' alt="{tr}Clear{/tr}"}</a>
						{else}
							{icon _id='bin_empty' alt="{tr}Clear{/tr}"}
						{/if}
						<a title="{tr}Delete{/tr}" class="link remove confirm-prompt" href="{service controller=tracker action=remove trackerId=$tracker.trackerId}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
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
