{* $Id$ *}

{title help="Trackers" admpage="trackers"}{tr}Trackers{/tr}{/title}

<div class="navbar">
	{include file="tracker_actions.tpl"}
</div>

{tabset name='tabs_trackers'}

{* --- tab with list --- *}
{tab name="{tr}Trackers{/tr}"}
<a name="view"></a>
	{if ($trackers) or ($find)}
		{include file='find.tpl' filters=''}
		{if ($find) and ($trackers)}
			<p>{tr}Found{/tr} {$trackers|@count} {tr}trackers:{/tr}</p>
		{/if}
	{/if}

	<table class="normal">
		<tr>
			<th>{self_link _sort_arg='sort_mode' _sort_field='trackerId'}{tr}Id{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Last Modif{/tr}{/self_link}</th>
			<th style="text-align:right;">{self_link _sort_arg='sort_mode' _sort_field='items'}{tr}Items{/tr}{/self_link}</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{foreach from=$trackers item=tracker}
			<tr>
				<td class="id">
					{$tracker.trackerId|escape}
				</td>
				<td class="text">
					<a class="tablename" href="tiki-view_tracker.php?trackerId={$tracker.trackerId}" title="{tr}View{/tr}">{$tracker.name|escape}</a>
					<div class="description">
						{if $tracker.descriptionIsParsed eq 'y'}
							{wiki}{$tracker.description}{/wiki}
						{else}
							{$tracker.description|escape|nl2br}
						{/if}
					</div>
				</td>
				<td class="date">{$tracker.created|tiki_short_date}</td>
				<td class="date">{$tracker.lastModif|tiki_short_date}</td>
				<td class="integer">{$tracker.items|escape}</td>
				<td class="action">
					{if $tracker.permissions->export_tracker}
						<a title="{tr _0=$tracker.name|escape}Export %0{/tr}" class="export dialog" href="{service controller=tracker action=export trackerId=$tracker.trackerId}">{icon _id='disk' alt="{tr}Export{/tr}"}</a>
					{/if}
					{if $tracker.permissions->admin_trackers}
						<a title="{tr _0=$tracker.name|escape}Import in %0{/tr}" class="import dialog" href="{service controller=tracker action=import_items trackerId=$tracker.trackerId}">{icon _id='upload' alt="{tr}Import{/tr}"}</a>
						<a title="{tr _0=$tracker.name|escape}Events{/tr}" class="event dialog" href="{service controller=tracker_todo action=view trackerId=$tracker.trackerId}">{icon _id='clock' alt="{tr}Events{/tr}"}</a>
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
						<a title="{tr}Edit{/tr}" class="edit dialog" href="{service controller=tracker action=replace trackerId=$tracker.trackerId}">{icon _id='pencil' alt="{tr}Edit{/tr}"}</a>
						{if $tracker.individual eq 'y'}
							<a title="{tr}Active Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$tracker.name|escape:"url"}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$tracker.trackerId}">{icon _id='key_active' alt="{tr}Active Permissions{/tr}"}</a>
						{else}
							<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$tracker.name|escape:"url"}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$tracker.trackerId}">{icon _id='key' alt="{tr}Permissions{/tr}"}</a>
						{/if}
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
				{norecords _colspan=7 _text="No records found with: $find"}
			{else}
				{norecords _colspan=7}
			{/if}
		{/foreach}
	</table>
	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
	{if $tiki_p_admin_trackers eq 'y'}
		<form class="create-tracker" method="post" action="{service controller=tracker action=replace}">
			<input type="submit" value="{tr}Create tracker{/tr}"/>
		</form>
	{/if}
	{if !empty($trackerId)}
		<div id="trackeredit"></div>
		{jq}
			$("#trackeredit").serviceDialog({
				title:'{{$trackerInfo.name|escape:javascript}}',
				data: {
					controller: 'tracker',
					action: 'replace',
					trackerId: {{$trackerId}}
				}
			});
		{/jq}
	{/if}
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
		$('.edit.dialog').click(function () {
			var link = this;
			$(this).serviceDialog({
				title: $(link).closest('tr').find('.text a').text(),
				data: {
					controller: 'tracker',
					action: 'replace',
					trackerId: parseInt($(link).closest('tr').find('.id').text(), 10)
				},
				load: function() {
					$(".tree.root:not(.init)", this).browse_tree().addClass("init");
				}
			});

			return false;
		});

		$('.export.dialog').click(function () {
			var link = this;
			$(this).serviceDialog({
				title: $(link).attr('title'),
				data: {
					controller: 'tracker',
					action: 'export',
					trackerId: parseInt($(link).closest('tr').find('.id').text(), 10)
				}
			});

			return false;
		});

		$('.event.dialog').click(function () {
			var link = this;
			$(this).serviceDialog({
				title: $(link).attr('title'),
				data: {
					controller: 'tracker_todo',
					action: 'view',
					trackerId: parseInt($(link).closest('tr').find('.id').text(), 10)
				}
			});

			return false;
		});

		$('.import.dialog').click(function () {
			var link = this;
			$(this).serviceDialog({
				title: $(link).attr('title'),
				data: {
					controller: 'tracker',
					action: 'import_items',
					trackerId: parseInt($(link).closest('tr').find('.id').text(), 10)
				}
			});

			return false;
		});

		$('.create-tracker').submit(function () {
			var form = this;
			$(this).serviceDialog({
				title: $(':submit', form).val(),
				data: {
					controller: 'tracker',
					action: 'replace'
				},
				success: function () {
					document.location.reload();
				},
				load: function() {
					$(".tree.root:not(.init)", this).browse_tree().addClass("init");
				}
			});

			return false;
		});
	{/jq}
{/tab}
{if $tiki_p_admin_trackers eq 'y'}
{tab name="{tr}Duplicate/Import Tracker{/tr}"}
{* --- tab with raw form --- *}
	<h2>{tr}Duplicate Tracker{/tr}</h2>

	<form class="simple" action="{service controller=tracker action=duplicate}" method="post">
		<label>
			{tr}Name{/tr}
			<input type="text" name="name" />
		</label>
		<label>
			{tr}Tracker{/tr}
			<select name="trackerId">
				{foreach from=$trackers item=tr}
					<option value="{$tr.trackerId|escape}">{$tr.name|escape}</option>
				{/foreach}
			</select>
		</label>
		{if $prefs.feature_categories eq 'y'}
			<label>
				<input type="checkbox" name="dupCateg" value="1"/>
				{tr}Duplicate categories{/tr}
			</label>
		{/if}
		<label>
			<input type="checkbox" name="dupPerms" value="1"/>
			{tr}Duplicate permissions{/tr}
		</label>
		<div class="submit">
			<input type="submit" value="{tr}Duplicate{/tr}"/>
		</div>
	</form>
	
	{if $prefs.tracker_remote_sync eq 'y'}
		<h2>{tr}Duplicate Remote Tracker{/tr}</h2>
		<form class="simple" method="post" action="{service controller=tracker_sync action=clone_remote}">
			<label>
				{tr}URL:{/tr}
				<input type="url" name="url" required="required"/>
			</label>
			<div>
				<input type="submit" value="{tr}Search for trackers to clone{/tr}"/>
			</div>
		</form>
	{/if}

	<div class="importFromExport">
		<h2>{tr}Import From Export{/tr}</h2>
		<h4>{tr}Import from CSV{/tr}</h4>
		<form class="simple" method="post" action="{service controller=tracker action=import}">
			<label>
				{tr}Raw data{/tr}
				<textarea name="raw" rows="20"></textarea>
			</label>
			<label>
				<input type="checkbox" name="preserve" value="1"/>
				{tr}Preserve tracker ID{/tr}
			</label>
			<div class="submit">
				<input type="submit" value="{tr}Import{/tr}"/>
			</div>
		</form>

        <h4>{tr}Import From Profile/YAML{/tr}</h4>
        <div>
	        <form id="forumImportFromProfile" action="{service controller=tracker action=import_profile trackerId=$trackerId}" method="post" enctype="multipart/form-data">
				{remarksbox type="info" title="{tr}New Feature{/tr}" icon="bricks"}
	                <p><em>{tr}Please note: Experimental - work in progress{/tr}</em></p>
				{/remarksbox}
	            <label style="display: block">
					{tr}YAML{/tr}
	            </label>
	            <textarea name="yaml" id="importFromProfileYaml" data-codemirror="true" data-syntax="yaml" data-line-numbers="true" style="height: 400px;"></textarea>
                <div class="submit">
                    <input type="submit" value="{tr}Import{/tr}"/>
                </div>
            </form>
        </div>
	</div>
{/tab}
{/if}

{/tabset}

{jq}
	$('.importFromExport').visible(function() {
		$(this).accordion({
			header: 'h4'
		});
	});
	$('#forumImportFromProfile').submit(function() {
		$.modal(tr('Loading...'));
		$.post($(this).attr('action'), {yaml: $('#importFromProfileYaml').val()}, function() {
			document.location = document.location + '';
		});
		return false;
	});
{/jq}