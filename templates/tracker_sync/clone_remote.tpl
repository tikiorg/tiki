{if $trackerId}
	<a href="tiki-admin_tracker_fields.php?trackerId={$trackerId|escape:'url'}">{tr}Admin Fields{/tr}</a>
{else}
	<form method="post" action="tiki-ajax_services.php" class="simple">
		{if $list}
			<label>
				{tr}Tracker:{/tr}
				<select name="remote_tracker_id">
					{foreach from=$list key=id item=label}
						<option value="{$id|escape}">{$label|escape}</option>
					{/foreach}
				</select>
				<input type="hidden" name="url" value="{$url|escape}"/>
			</label>
		{else}
			<label>
				{tr}URL:{/tr}
				<input type="url" name="url" value="{$url|escape}" required="required"/>
				<div class="description">
					{tr}It is very likely that authentication will be required to access this data on the remote site. Configure the authentication source from Admin DSN.{/tr}
				</div>
			</label>
		{/if}
		<div>
			<input type="hidden" name="controller" value="tracker_sync"/>
			<input type="hidden" name="action" value="clone_remote"/>
			{if $list}
				<input type="submit" value="{tr}Clone{/tr}"/>
			{else}
				<input type="submit" value="{tr}Search for trackers to clone{/tr}"/>
			{/if}
		</div>
	</form>
{/if}
