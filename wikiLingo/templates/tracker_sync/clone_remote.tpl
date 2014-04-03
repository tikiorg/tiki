{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $trackerId}
		<a href="tiki-admin_tracker_fields.php?trackerId={$trackerId|escape:'url'}">{tr}Admin Fields{/tr}</a>
	{else}
		<form method="post" action="{service controller=tracker_sync action=clone_remote}" class="simple" role="form">
			{if $list}
				<label>
					{tr}Tracker:{/tr}
					<select name="remote_tracker_id">
						{foreach from=$list key=id item=label}
							<option value="{$id|escape}">{$label|escape}</option>
						{/foreach}
					</select>
					<input type="hidden" name="url" value="{$url|escape}">
				</label>
			{else}
				<label>
					{tr}URL:{/tr}
					<input type="url" name="url" value="{$url|escape}" required="required">
					<div class="description help-block">
						{tr}It is very likely that authentication will be required to access this data on the remote site. Configure the authentication source from Admin DSN.{/tr}
					</div>
				</label>
			{/if}
			<div class="submit text-center">
				{if !$modal}
					<a href="tiki-list_trackers.php" class="btn btn-link">{tr}Cancel{/tr}</a>
				{/if}
				{if $list}
					<input type="submit" class="btn btn-primary" value="{tr}Clone{/tr}">
				{else}
					<input type="submit" class="btn btn-primary" value="{tr}Search for trackers to clone{/tr}">
				{/if}
			</div>
		</form>
	{/if}
{/block}
