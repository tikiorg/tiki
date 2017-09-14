{activityframe activity=$activity heading="{tr _0=$activity.user|userlink}%0 modified a page{/tr}"}
	<p>
		{object_link type=$activity.type id=$activity.object}<br>
		{if $activity.edit_comment}<span class="description">{$activity.edit_comment|escape}</span>{/if}
	</p>
	<small>{tr}View changes:{/tr} <a href="tiki-pagehistory.php?page={$activity.object}&oldver={$activity.old_version|escape}&newver={$activity.version|escape}">{tr}history{/tr}</small>
	{if is_array($activity.aggregate)}
	<small>{$activity.aggregate.user|userlink}</small>
	{/if}
{/activityframe}
