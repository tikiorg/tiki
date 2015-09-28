{activityframe activity=$activity heading="{tr _0=$activity.user|userlink}%0 posted a comment to{/tr}"}
	<p>{object_link type=$activity.type id=$activity.object}</p>
	{if is_array($activity.aggregate)}
	<small>{$activity.aggregate.user|userlink}</small>
	{/if}
{/activityframe}
