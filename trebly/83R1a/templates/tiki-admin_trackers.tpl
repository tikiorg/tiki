{* $Id: tiki-admin_trackers.tpl 37917 2011-10-03 16:51:59Z lphuberdeau $ *}

{title help="Trackers" admpage="trackers"}{tr}Admin Trackers{/tr}{/title}

<div class="navbar">
	{include file="tracker_actions.tpl"}
</div>

{tabset name='tabs_admtrackers'}

{* --- tab with list --- *}
{tab name="{tr}Trackers{/tr}"}
<a name="view"></a>
	<h2>{tr}Trackers{/tr}</h2>
	{if ($channels) or ($find)}
		{include file='find.tpl' filters=''}
		{if ($find) and ($channels)}
			<p>{tr}Found{/tr} {$channels|@count} {tr}trackers:{/tr}</p>
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
		{foreach from=$channels item=tracker}
			<tr>
				<td class="id">
					{$tracker.trackerId|escape}
				</td>
				<td class="text">
					<a class="tablename dialog" href="{service controller=tracker action=replace trackerId=$tracker.trackerId}" title="{tr}Edit{/tr}">{$tracker.name|escape}</a>
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
					<a title="{tr _0=$tracker.name|escape}Export %0{/tr}" class="export dialog" href="{service controller=tracker action=export trackerId=$tracker.trackerId}">{icon _id='disk' alt="{tr}Export{/tr}"}</a>
					<a title="{tr _0=$tracker.name|escape}Import in %0{/tr}" class="import dialog" href="{service controller=tracker action=import_items trackerId=$tracker.trackerId}">{icon _id='upload' alt="{tr}Import{/tr}"}</a>
					<a title="{tr _0=$tracker.name|escape}Events{/tr}" class="event dialog" href="{service controller=tracker_todo action=view trackerId=$tracker.trackerId}">{icon _id='clock' alt="{tr}Events{/tr}"}</a>
					<a title="{tr}View{/tr}" href="tiki-view_tracker.php?trackerId={$tracker.trackerId}">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>
					<a title="{tr}Fields{/tr}" class="link" href="tiki-admin_tracker_fields.php?trackerId={$tracker.trackerId}">{icon _id='table' alt="{tr}Fields{/tr}"}</a>
					{if $tracker.individual eq 'y'}
						<a title="{tr}Active Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$tracker.name|escape:"url"}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$tracker.trackerId}">{icon _id='key_active' alt="{tr}Active Permissions{/tr}"}</a>
					{else}
						<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$tracker.name|escape:"url"}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$tracker.trackerId}">{icon _id='key' alt="{tr}Permissions{/tr}"}</a>
					{/if}
					<a title="{tr}Delete{/tr}" class="link remove confirm-prompt" href="{service controller=tracker action=remove trackerId=$tracker.trackerId}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
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
	<form class="create-tracker" method="post" action="{service controller=tracker action=replace}">
		<input type="submit" value="{tr}Create tracker{/tr}"/>
	</form>
	{jq}
		$('.remove.confirm-prompt').requireConfirm({
			message: "{tr}Do you really remove this tracker?{/tr}",
			success: function (data) {
				$(this).closest('tr').remove();
			}
		});
		$('.tablename.dialog').click(function () {
			var link = this;
			$(this).serviceDialog({
				title: $(link).text(),
				data: {
					controller: 'tracker',
					action: 'replace',
					trackerId: parseInt($(link).closest('tr').find('.id').text(), 10)
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
				}
			});

			return false;
		});
	{/jq}
{/tab}

{tab name="{tr}Duplicate Tracker{/tr}"}
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

	<h2>{tr}Import From Export{/tr}</h2>
	<form class="simple" method="post" action="{service controller=tracker action=import}">
		<label>
			{tr}Raw data{/tr}
			<textarea name="raw"></textarea>
		</label>
		<label>
			<input type="checkbox" name="preserve" value="1"/>
			{tr}Preserve tracker ID{/tr}
		</label>
		<div class="submit">
			<input type="submit" value="{tr}Import{/tr}"/>
		</div>
	</form>
{/tab}

{/tabset}
